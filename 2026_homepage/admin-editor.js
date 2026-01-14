document.addEventListener('DOMContentLoaded', function() {
    // 페이지 ID가 body 태그에 없으면 실행 중단
    const pageId = document.body.dataset.pageId;
    if (!pageId) return;

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
                editorField = document.createElement('textarea');
                editorField.value = originalContent.replace(/<br\s*\/?>/gi, '\n'); // <br> 태그를 줄바꿈 문자로 변환
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
            editorField.focus();

            // '저장' 버튼 클릭 이벤트
            saveButton.addEventListener('click', function() {
                const newContent = isMultiLine ? editorField.value.replace(/\n/g, '<br>') : editorField.value;

                const formData = new FormData();
                formData.append('page_id', pageId);
                formData.append('element_id', elementId);
                formData.append('content', newContent);

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
                editorWrapper.remove();
                el.style.display = ''; // 숨겼던 원래 요소를 다시 표시
                el.classList.remove('editor-active');
            }
        });
    });
});