document.addEventListener('DOMContentLoaded', function() {
    // TinyMCE 라이브러리 동적 로드
    const script = document.createElement('script');
    // 실제 운영 시에는 https://www.tiny.cloud/ 에서 API 키를 발급받아 'no-api-key' 부분을 교체해주세요.
    script.src = 'https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js';
    script.referrerPolicy = 'origin';
    document.head.appendChild(script);

    script.onload = () => {
        tinymce.init({
            selector: '[data-editable-id]',
            inline: true,
            menubar: false,
            plugins: 'link autolink lists',
            toolbar: 'bold italic underline | bullist numlist | link | undo redo',
            
            // 콘텐츠 저장 로직
            setup: function(editor) {
                editor.on('blur', function() {
                    const element = editor.getElement();
                    const elementId = element.dataset.editableId;
                    const pageId = document.body.dataset.pageId; // body 태그에 data-page-id="페이지ID" 필요
                    const newContent = editor.getContent();

                    if (!pageId) {
                        console.error('Body 태그에 data-page-id 속성이 정의되지 않았습니다.');
                        return;
                    }

                    // 서버로 데이터 전송
                    const formData = new FormData();
                    formData.append('page_id', pageId);
                    formData.append('element_id', elementId);
                    formData.append('content', newContent);

                    fetch('ajax.content.update.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // 성공 시 시각적 피드백 (예: 잠시 배경색 변경)
                            element.style.transition = 'background-color 0.5s';
                            element.style.backgroundColor = '#e6ffed';
                            setTimeout(() => {
                                element.style.backgroundColor = '';
                            }, 1500);
                        } else {
                            alert('콘텐츠 저장에 실패했습니다: ' + data.message);
                        }
                    })
                    .catch(error => alert('서버와 통신 중 오류가 발생했습니다.'));
                });
            }
        });
    };
});