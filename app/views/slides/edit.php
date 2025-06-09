<?php
require_once __DIR__ . '/../../helpers/SlideRenderer.php';
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/slides.css">
    <title>Редактиране на слайд</title>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Редактиране на слайд</h1>
            
            <?php if (isset($data['error'])): ?>
                <div class="error"><?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <div class="editor-preview-container">
                <div class="editor-section">
                    <form action="<?= BASE_URL ?>/slides/edit/<?= $data['slide']['id'] ?>" method="POST">
                        <input type="hidden" name="id" value="<?= $data['slide']['id'] ?>">
                        <input type="hidden" name="presentation_id" value="<?= $data['slide']['presentation_id'] ?>">
                        
                        <div class="form-group">
                            <label for="title">Заглавие на слайда:</label>
                            <input type="text" id="title" name="title" value="<?= htmlspecialchars($data['slide']['title']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="layout">Изберете оформление:</label>
                            <select id="layout" name="layout" onchange="updateLayout()">
                                    <option value="full" <?= $data['slide']['layout'] === 'full' ? 'selected' : '' ?>>Пълен екран</option>
                                    <option value="two-columns" <?= $data['slide']['layout'] === 'two-columns' ? 'selected' : '' ?>>Две колони</option>
                                    <option value="three-columns" <?= $data['slide']['layout'] === 'three-columns' ? 'selected' : '' ?>>Три колони</option>
                                    <option value="two-rows" <?= $data['slide']['layout'] === 'two-rows' ? 'selected' : '' ?>>Две реда</option>
                                    <option value="three-rows" <?= $data['slide']['layout'] === 'three-rows' ? 'selected' : '' ?>>Три реда</option>
                                    <option value="grid-2x2" <?= $data['slide']['layout'] === 'grid-2x2' ? 'selected' : '' ?>>Мрежа 2x2</option>
                                    <option value="grid-2x3" <?= $data['slide']['layout'] === 'grid-2x3' ? 'selected' : '' ?>>Мрежа 2x3</option>
                                    <option value="grid-2x4" <?= $data['slide']['layout'] === 'grid-2x4' ? 'selected' : '' ?>>Мрежа 2x4</option>
                                    <option value="grid-3x2" <?= $data['slide']['layout'] === 'grid-3x2' ? 'selected' : '' ?>>Мрежа 3x2</option>
                                    <option value="grid-3x3" <?= $data['slide']['layout'] === 'grid-3x3' ? 'selected' : '' ?>>Мрежа 3x3</option>
                                    <option value="grid-4x2" <?= $data['slide']['layout'] === 'grid-4x2' ? 'selected' : '' ?>>Мрежа 4x2</option>
                            </select>
                        </div>

                        <div id="content-elements">
                            <!-- Content elements will be added here -->
                        </div>

                        <button type="submit" class="btn my-3">Запази промените</button>
                        <a href="<?= BASE_URL ?>/presentation/viewPresentation/<?= $data['slide']['presentation_id'] ?>" class="btn btn-secondary my-3">Отказ</a>
                    </form>
                </div>

                <div class="preview-section">
                    <h3>Предварителен преглед</h3>
                    <div class="slide-preview" id="slidePreview">
                        <!-- Тук ще се рендерира прегледа на слайда -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const layoutSelect = document.getElementById('layout');
        const contentElements = document.getElementById('content-elements');
        const slidePreview = document.getElementById('slidePreview');
        const titleInput = document.getElementById('title');

        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Initialize with existing elements
        const existingElements = <?= json_encode($data['slide']['elements'] ?? []) ?>;
        
        // Add existing elements on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, initializing elements');
            updateLayout();
        });

        // Add form submission handling
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            const formData = new FormData(this);
            const formDataObj = {};
            formData.forEach((value, key) => {
                console.log(`${key}: ${value}`);
                formDataObj[key] = value;
            });
            
            console.log('Form data object:', formDataObj);
            console.log('Form action URL:', this.action);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(formData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.json();
            })
            .then(data => {
                console.log('Server response:', data);
                if (data.success) {
                    const successDiv = document.createElement('div');
                    successDiv.className = 'success';
                    successDiv.textContent = data.message;
                    const existingMessages = document.querySelector('.error, .success');
                    if (existingMessages) {
                        existingMessages.remove();
                    }
                    document.querySelector('.form-container').insertBefore(successDiv, document.querySelector('.editor-preview-container'));
                    
                    // Redirect after successful edit
                    setTimeout(() => {
                        window.location.href = `<?= BASE_URL ?>/presentation/viewPresentation/<?= $data['slide']['presentation_id'] ?>`;
                    }, 1500);
                } else {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error';
                    errorDiv.textContent = data.message || 'Възникна грешка при редактирането на слайда';
                    const existingMessages = document.querySelector('.error, .success');
                    if (existingMessages) {
                        existingMessages.remove();
                    }
                    document.querySelector('.form-container').insertBefore(errorDiv, document.querySelector('.editor-preview-container'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error';
                errorDiv.textContent = 'Възникна грешка при изпращането на формата';
                document.querySelector('.form-container').insertBefore(errorDiv, document.querySelector('.editor-preview-container'));
            });
        });

        function createContentElement(index, type = 'text', title = '', content = '', text = '', style = null) {
            console.log('Creating content element:', { index, type, title, content, text, style });
            const element = document.createElement('div');
            element.className = 'content-element';
            element.innerHTML = `
                <h4>Елемент ${index + 1}</h4>
                <select class="content-type" name="elements[${index}][type]" onchange="updateContentFields(this.parentElement, this.value)">
                    <option value="text" ${type === 'text' ? 'selected' : ''}>Текст</option>
                    <option value="image" ${type === 'image' ? 'selected' : ''}>Изображение</option>
                    <option value="image_text" ${type === 'image_text' ? 'selected' : ''}>Изображение и текст</option>
                    <option value="image_list" ${type === 'image_list' ? 'selected' : ''}>Изображение и списък</option>
                    <option value="list" ${type === 'list' ? 'selected' : ''}>Списък</option>
                    <option value="quote" ${type === 'quote' ? 'selected' : ''}>Цитат</option>
                </select>
                <div class="content-fields">
                    <input type="text" class="content-title" name="elements[${index}][title]" placeholder="Заглавие (по желание)" value="${escapeHtml(title)}">
                    <input type="hidden" name="elements[${index}][style]" value='${JSON.stringify(style || {})}'>
                    ${type === 'image_text' ? `
                        <div class="image-text-fields">
                            <div class="image-field">
                                <label>Изображение:</label>
                                <input type="url" class="content-content" name="elements[${index}][content]" placeholder="URL на изображението" value="${escapeHtml(content)}">
                            </div>
                            <div class="text-field">
                                <label>Текст:</label>
                                <textarea class="content-text" name="elements[${index}][text]" placeholder="Въведете текст">${escapeHtml(text)}</textarea>
                            </div>
                        </div>
                    ` : type === 'image_list' ? `
                        <div class="image-list-fields">
                            <div class="image-field">
                                <label>Изображение:</label>
                                <input type="url" class="content-content" name="elements[${index}][content]" placeholder="URL на изображението" value="${escapeHtml(content)}">
                            </div>
                            <div class="list-field">
                                <label>Списък:</label>
                                <textarea class="content-text" name="elements[${index}][text]" placeholder="Въведете елементи на списъка (по един на ред)">${escapeHtml(text)}</textarea>
                            </div>
                        </div>
                    ` : type === 'image' ? `
                        <input type="url" class="content-content" name="elements[${index}][content]" placeholder="URL на изображението" value="${escapeHtml(content)}">
                    ` : type === 'list' ? `
                        <textarea class="content-content" name="elements[${index}][content]" placeholder="Въведете елементи на списъка (по един на ред)">${escapeHtml(text || content)}</textarea>
                    ` : type === 'quote' ? `
                        <textarea class="content-content" name="elements[${index}][content]" placeholder="Въведете цитат">${escapeHtml(text || content)}</textarea>
                    ` : `
                        <textarea class="content-content" name="elements[${index}][content]" placeholder="Въведете текст">${escapeHtml(text || content)}</textarea>
                    `}
                </div>
            `;

            // Add event listener for content type change
            const contentTypeSelect = element.querySelector('.content-type');
            contentTypeSelect.addEventListener('change', function() {
                updateContentFields(element, this.value);
                updatePreview();
            });

            // Add event listeners for content changes
            element.querySelectorAll('input, textarea').forEach(input => {
                input.addEventListener('input', updatePreview);
            });

            return element;
        }

        function updateStyle(index, property, value) {
            const styleInput = document.querySelector(`input[name="elements[${index}][style]"]`);
            const currentStyle = JSON.parse(styleInput.value || '{}');
            currentStyle[property] = value;
            styleInput.value = JSON.stringify(currentStyle);
            updatePreview();
        }

        function updateContentFields(element, type) {
            const contentFields = element.querySelector('.content-fields');
            const titleInput = element.querySelector('.content-title');
            const contentInput = element.querySelector('.content-content');
            const textTextarea = element.querySelector('.content-text');
            const styleInput = element.querySelector('input[name^="elements["][name$="][style]"]');
            const index = element.querySelector('.content-type').name.match(/\[(\d+)\]/)[1];
            const currentType = element.querySelector('.content-type').value;

            // Store current values
            const currentTitle = titleInput.value;
            const currentContent = contentInput ? contentInput.value : '';
            const currentText = textTextarea ? textTextarea.value : '';
            const currentStyle = JSON.parse(styleInput.value || '{}');

            // Clear existing fields except title and style
            while (contentFields.children.length > 2) {
                contentFields.removeChild(contentFields.lastChild);
            }

            // Update title placeholder and add appropriate fields
            switch (type) {
                case 'text':
                    titleInput.placeholder = 'Заглавие (по желание)';
                    if (currentType === 'quote') {
                        titleInput.value = currentTitle;
                    }
                    contentFields.innerHTML += `
                        <textarea class="content-content" name="elements[${index}][content]" placeholder="Въведете текст">${currentText || currentContent}</textarea>
                    `;
                    break;
                case 'image':
                    titleInput.placeholder = 'Заглавие на изображението (по желание)';
                    if (currentType === 'text' || currentType === 'list' || currentType === 'quote') {
                        titleInput.value = currentTitle;
                    }
                    contentFields.innerHTML += `
                        <input type="url" class="content-content" name="elements[${index}][content]" placeholder="URL на изображението" value="${currentContent}">
                    `;
                    break;
                case 'image_text':
                    titleInput.placeholder = 'Заглавие (по желание)';
                    if (currentType === 'text' || currentType === 'list' || currentType === 'quote') {
                        titleInput.value = currentTitle;
                    }
                    contentFields.innerHTML += `
                        <div class="image-text-fields">
                            <div class="image-field">
                                <label>Изображение:</label>
                                <input type="url" class="content-content" name="elements[${index}][content]" placeholder="URL на изображението" value="${currentContent}">
                            </div>
                            <div class="text-field">
                                <label>Текст:</label>
                                <textarea class="content-text" name="elements[${index}][text]" placeholder="Въведете текст">${currentText || currentContent}</textarea>
                            </div>
                        </div>
                    `;
                    break;
                case 'image_list':
                    titleInput.placeholder = 'Заглавие (по желание)';
                    if (currentType === 'text' || currentType === 'list' || currentType === 'quote') {
                        titleInput.value = currentTitle;
                    }
                    contentFields.innerHTML += `
                        <div class="image-list-fields">
                            <div class="image-field">
                                <label>Изображение:</label>
                                <input type="url" class="content-content" name="elements[${index}][content]" placeholder="URL на изображението" value="${currentContent}">
                            </div>
                            <div class="list-field">
                                <label>Списък:</label>
                                <textarea class="content-text" name="elements[${index}][text]" placeholder="Въведете елементи на списъка (по един на ред)">${currentText || currentContent}</textarea>
                            </div>
                        </div>
                    `;
                    break;
                case 'list':
                    titleInput.placeholder = 'Заглавие на списъка (по желание)';
                    if (currentType === 'text' || currentType === 'quote') {
                        titleInput.value = currentTitle;
                    }
                    contentFields.innerHTML += `
                        <textarea class="content-content" name="elements[${index}][content]" placeholder="Въведете елементи на списъка (по един на ред)">${currentText || currentContent}</textarea>
                    `;
                    break;
                case 'quote':
                    titleInput.placeholder = 'Автор на цитата (по желание)';
                    if (currentType === 'text' || currentType === 'list') {
                        titleInput.value = currentTitle;
                    }
                    contentFields.innerHTML += `
                        <textarea class="content-content" name="elements[${index}][content]" placeholder="Въведете цитат">${currentText || currentContent}</textarea>
                    `;
                    break;
            }

            // Add event listeners to new fields
            element.querySelectorAll('input, textarea').forEach(input => {
                input.addEventListener('input', updatePreview);
            });

            // Force preview update
            setTimeout(() => {
                const newContentInput = contentFields.querySelector('.content-content');
                const newTextTextarea = contentFields.querySelector('.content-text');
                
                if (newContentInput && type === 'list') {
                    newContentInput.value = currentText || currentContent;
                }
                
                if (newTextTextarea && (type === 'image_text' || type === 'image_list')) {
                    newTextTextarea.value = currentText || currentContent;
                }
                
                updatePreview();
            }, 0);
        }

        function updateLayout() {
            const layout = layoutSelect.value;
            const preview = document.getElementById('slidePreview');
            preview.className = 'slide-preview';
            
            // Clear existing elements
            contentElements.innerHTML = '';
            
            // Create elements based on layout
            let elementCount = 0;
            switch (layout) {
                case 'full':
                    elementCount = 1;
                    break;
                case 'two-columns':
                case 'two-rows':
                    elementCount = 2;
                    break;
                case 'three-columns':
                case 'three-rows':
                    elementCount = 3;
                    break;
                case 'grid-2x2':
                    elementCount = 4;
                    break;
                case 'grid-2x3':
                    elementCount = 6;
                    break;
                case 'grid-2x4':
                    elementCount = 8;
                    break;
                case 'grid-3x2':
                    elementCount = 6;
                    break;
                case 'grid-3x3':
                    elementCount = 9;
                    break;
                case 'grid-4x2':
                    elementCount = 8;
                    break;
            }
            
            // Create new elements or use existing ones
            for (let i = 0; i < elementCount; i++) {
                const existingElement = existingElements[i];
                const element = createContentElement(
                    i,
                    existingElement?.type || 'text',
                    existingElement?.title || '',
                    existingElement?.content || '',
                    existingElement?.text || '',
                    existingElement?.style || null
                );
                contentElements.appendChild(element);
            }
            
            updatePreview();
        }

        function updatePreview() {
            const preview = document.getElementById('slidePreview');
            const title = titleInput.value;
            const layout = layoutSelect.value;
            
            let previewHtml = `
                <div class="slide">
                    <h2 class="slide-title">${escapeHtml(title)}</h2>
                    <div class="slide-content ${layout}">
            `;

            const elements = Array.from(contentElements.children);
            elements.forEach(element => {
                const type = element.querySelector('.content-type').value;
                const title = element.querySelector('.content-title').value;
                const content = element.querySelector('.content-content')?.value || '';
                const text = element.querySelector('.content-text')?.value || '';
                const style = JSON.parse(element.querySelector('input[name^="elements["][name$="][style]"]').value || '{}');

                let elementHtml = `<div class="content-element type-${type}" style="`;
                if (style.color) elementHtml += `color: ${style.color};`;
                if (style.fontSize) elementHtml += `font-size: ${style.fontSize};`;
                if (style.textAlign) elementHtml += `text-align: ${style.textAlign};`;
                elementHtml += '">';

                if (title) {
                    elementHtml += `<h3>${escapeHtml(title)}</h3>`;
                }

                switch (type) {
                    case 'text':
                        elementHtml += `<p>${escapeHtml(content).replace(/\n/g, '<br>')}</p>`;
                        break;
                    case 'image':
                        elementHtml += `
                            <div class="image-container" style="background-image: url('${escapeHtml(content)}');"></div>
                        `;
                        break;
                    case 'image_text':
                        elementHtml += `
                            <div class="image-text-container">
                                <div class="image-container" style="background-image: url('${escapeHtml(content)}');"></div>
                                <div class="text"><p>${escapeHtml(text).replace(/\n/g, '<br>')}</p></div>
                            </div>
                        `;
                        break;
                    case 'image_list':
                        const listItems = text.split('\n').filter(item => item.trim());
                        elementHtml += `
                            <div class="image-list-container">
                                <div class="image-container" style="background-image: url('${escapeHtml(content)}');"></div>
                                <ul>
                                    ${listItems.map(item => `<li>${escapeHtml(item.trim())}</li>`).join('')}
                                </ul>
                            </div>
                        `;
                        break;
                    case 'list':
                        const items = content.split('\n').filter(item => item.trim());
                        elementHtml += `
                            <ul>
                                ${items.map(item => `<li>${escapeHtml(item.trim())}</li>`).join('')}
                            </ul>
                        `;
                        break;
                    case 'quote':
                        elementHtml += `
                            <blockquote>
                                ${escapeHtml(content).replace(/\n/g, '<br>')}
                                ${title ? `<cite>— ${escapeHtml(title)}</cite>` : ''}
                            </blockquote>
                        `;
                        break;
                }

                elementHtml += '</div>';
                previewHtml += elementHtml;
            });

            previewHtml += `
                    </div>
                </div>
            `;

            preview.innerHTML = previewHtml;
        }
    </script>
</body>
</html>