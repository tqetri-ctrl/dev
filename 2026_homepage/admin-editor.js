document.addEventListener('DOMContentLoaded', function() {
    // 페이지 ID가 body 태그에 없으면 실행 중단
    const pageId = document.body.dataset.pageId;
    if (!pageId) return;

    // admin.php와 유사한 CKEditor 설정을 정의합니다. 인라인 편집에 맞게 툴바를 일부 조정했습니다.
    const ckeditorConfig = {
        removePlugins: [
            'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges', 'RealTimeCollaborativeRevisionHistory', 'RealTimeCollaborativeEditing', 'Comments', 'TrackChanges', 'TrackChangesData', 'RevisionHistory', 'PresenceList',
            'WProofreader', 'AIAssistant', 'MathType', 'CKFinder', 'EasyImage', 'CKBox', 'CloudServices',
            'ExportPdf', 'ExportWord', 'ImportFromWord', 'Pagination', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents', 'Title', 'MediaEmbed', 'HtmlEmbed', 'SourceEditing'
        ],
        toolbar: {
            items: [
                'heading', '|', 'bold', 'italic', 'underline', 'strikethrough', '|', 'link', 'removeFormat', 'blockQuote', '|', 'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', '|', 'bulletedList', 'numberedList', 'outdent', 'indent', '|', 'uploadImage', 'insertTable', '|', 'undo', 'redo'
            ]
        },
        language: 'ko',
        simpleUpload: {
            uploadUrl: 'upload.php'
        }
    };

    // 현재 활성화된 에디터 인스턴스를 추적하기 위한 변수
    let activeEditorInstance = null;

    // 편집 가능한 모든 요소를 선택
    const editableElements = document.querySelectorAll('[data-editable-id]');

    editableElements.forEach(el => {
        el.classList.add('editable-element');
        el.title = '클릭하여 내용 수정';

        el.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // 이미 다른 요소를 편집 중이라면, 추가 편집을 막음
            if (document.querySelector('.editor-active')) {
                return;
            }

            const elementId = el.dataset.editableId;
            const originalContent = el.innerHTML;
            // 내용에 <br> 태그가 있거나, 내용이 길거나, 단락(p) 태그인 경우 여러 줄 편집(textarea)을 사용
            const isMultiLine = originalContent.includes('<br>') || originalContent.length > 100 || el.tagName === 'P';

            el.classList.add('editor-active');
            el.style.display = 'none'; // 원래 요소를 숨김

            // 편집기 UI 생성
            const editorWrapper = document.createElement('div');
            editorWrapper.className = 'editor-wrapper';

            let editorField;
            if (isMultiLine) {
                // CKEditor를 적용할 div 요소를 생성합니다.
                editorField = document.createElement('div');
            } else {
                editorField = document.createElement('input');
                editorField.type = 'text';
                editorField.value = el.textContent;
            }
            editorField.className = 'editor-field';

            const buttonGroup = document.createElement('div');
            buttonGroup.className = 'editor-buttons';

            const saveButton = document.createElement('button');
            saveButton.textContent = '저장';
            saveButton.className = 'editor-save';

            const cancelButton = document.createElement('button');
            cancelButton.textContent = '취소';
            cancelButton.className = 'editor-cancel';

            buttonGroup.appendChild(saveButton);
            buttonGroup.appendChild(cancelButton);

            editorWrapper.appendChild(editorField);
            editorWrapper.appendChild(buttonGroup);

            el.parentNode.insertBefore(editorWrapper, el.nextSibling); // 원래 요소 바로 다음에 편집기를 삽입

            if (isMultiLine) {
                CKEDITOR.ClassicEditor
                    .create(editorField, ckeditorConfig)
                    .then(editor => {
                        activeEditorInstance = editor;
                        editor.setData(originalContent);
                    })
                    .catch(error => {
                        console.error('CKEditor for inline editor failed to load:', error);
                        alert('에디터 로딩에 실패했습니다. 기본 텍스트 모드로 전환합니다.');
                        // CKEditor 실패 시, 단순 textarea로 대체
                        const fallbackTextarea = document.createElement('textarea');
                        fallbackTextarea.className = 'editor-field';
                        fallbackTextarea.value = originalContent.replace(/<br\s*\/?>/gi, '\n');
                        editorWrapper.replaceChild(fallbackTextarea, editorField);
                        editorField = fallbackTextarea; // editorField 변수를 새 textarea로 교체
                    });
            } else {
                editorField.focus();
            }

            // '저장' 버튼 클릭 이벤트
            saveButton.addEventListener('click', function() {
                let newContent;
                if (isMultiLine && activeEditorInstance) {
                    newContent = activeEditorInstance.getData();
                } else {
                    newContent = isMultiLine ? editorField.value.replace(/\n/g, '<br>') : editorField.value;
                }
                const formData = new FormData();
                formData.append('page_id', pageId);
                formData.append('element_id', elementId);
                formData.append('content', newContent);

                // CSRF 토큰을 meta 태그에서 읽어와 FormData에 추가합니다.
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (csrfToken) {
                    formData.append('token', csrfToken);
                }

                fetch('/abni/ajax.content.update.php', { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            el.innerHTML = newContent; // 성공 시, 페이지 내용 즉시 업데이트
                            removeEditor();
                        } else {
                            alert('저장 실패: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('저장 중 오류가 발생했습니다.');
                    });
            });

            // '취소' 버튼 클릭 이벤트
            cancelButton.addEventListener('click', removeEditor);

            function removeEditor() {
                if (activeEditorInstance) {
                    activeEditorInstance.destroy().then(() => {
                        editorWrapper.remove();
                        el.style.display = ''; // 숨겼던 원래 요소를 다시 표시
                        el.classList.remove('editor-active');
                        activeEditorInstance = null;
                    });
                } else {
                    editorWrapper.remove();
                    el.style.display = ''; // 숨겼던 원래 요소를 다시 표시
                    el.classList.remove('editor-active');
                }
            }
        });
    });
});