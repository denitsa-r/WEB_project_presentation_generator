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
    <title>Създаване на слайд</title>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Създаване на слайд</h1>
            
            <?php if (isset($data['error'])): ?>
                <div class="error"><?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <?php if (isset($data['success'])): ?>
                <div class="success"><?= htmlspecialchars($data['success']) ?></div>
            <?php endif; ?>

            <div class="editor-preview-container">
                <div class="editor-section">
                    <form action="<?= BASE_URL ?>/slides/create/<?= $data['presentation']['id'] ?>" method="POST" id="createSlideForm">
                        <input type="hidden" name="presentation_id" value="<?= $data['presentation']['id'] ?>">
                        <input type="hidden" name="no_redirect" value="1">
                        
                        <div class="form-group">
                            <label for="title">Заглавие на слайда:</label>
                            <input type="text" id="title" name="title" required>
                        </div>

                        <div class="form-group">
                            <label for="layout">Изберете оформление:</label>
                            <select id="layout" name="layout" onchange="updateLayout()">
                                    <option value="full">Пълен екран</option>
                                    <option value="two-columns">Две колони</option>
                                    <option value="three-columns">Три колони</option>
                                    <option value="two-rows">Две реда</option>
                                    <option value="three-rows">Три реда</option>
                                    <option value="grid-2x2">Мрежа 2x2</option>
                                    <option value="grid-2x3">Мрежа 2x3</option>
                                    <option value="grid-2x4">Мрежа 2x4</option>
                                    <option value="grid-3x2">Мрежа 3x2</option>
                                    <option value="grid-3x3">Мрежа 3x3</option>
                                    <option value="grid-4x2">Мрежа 4x2</option>
                            </select>
                        </div>

                        <div id="content-elements">
                            <!-- Content elements will be added here -->
                        </div>

                        <button type="submit" class="btn my-3">Създай слайд</button>
                        <a href="<?= BASE_URL ?>/presentation/viewPresentation/<?= $data['presentation']['id'] ?>" class="btn btn-secondary my-3">Отказ</a>
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

        // Добавяме първия елемент при зареждане на страницата
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, initializing first element');
            const firstElement = createContentElement(0);
            contentElements.appendChild(firstElement);
            updateLayout();
        });

        // Добавяме логване при изпращане на формата
        document.getElementById('createSlideForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Предотвратяваме автоматичното изпращане
            console.log('Form submitted');
            
            // Събираме всички данни от формата
            const formData = new FormData(this);
            const formDataObj = {};
            formData.forEach((value, key) => {
                console.log(`${key}: ${value}`);
                formDataObj[key] = value;
            });
            
            console.log('Form data object:', formDataObj);
            console.log('Form action URL:', this.action);
            
            // Изпращаме формата чрез AJAX
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.json();
            })
            .then(data => {
                console.log('Server response:', data);
                if (data.success) {
                    // Показваме съобщение за успех
                    const successDiv = document.createElement('div');
                    successDiv.className = 'success';
                    successDiv.textContent = data.message;
                    const existingMessages = document.querySelector('.error, .success');
                    if (existingMessages) {
                        existingMessages.remove();
                    }
                    document.querySelector('.form-container').insertBefore(successDiv, document.querySelector('.editor-preview-container'));
                    
                    // Изчистваме формата
                    document.getElementById('createSlideForm').reset();
                    contentElements.innerHTML = '';
                    const firstElement = createContentElement(0);
                    contentElements.appendChild(firstElement);
                    updateLayout();
                } else {
                    // Показваме съобщение за грешка
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error';
                    errorDiv.textContent = data.message || 'Възникна грешка при създаването на слайда';
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
                    ` : type === 'image' ? `
                        <input type="url" class="content-content" name="elements[${index}][content]" placeholder="URL на изображението" value="${escapeHtml(content)}">
                    ` : `
                        <textarea class="content-content" name="elements[${index}][content]" placeholder="Съдържание">${escapeHtml(content)}</textarea>
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
            const index = element.querySelector('.content-type').name.match(/\[(\d+)\]/)[1];
            const styleInput = element.querySelector(`input[name="elements[${index}][style]"]`);

            // Store current values
            const currentTitle = titleInput.value;
            const currentContent = contentInput ? contentInput.value : '';
            const currentText = textTextarea ? textTextarea.value : '';
            const currentStyle = JSON.parse(styleInput.value || '{}');

            // Clear existing fields except title and style
            while (contentFields.children.length > 2) {
                contentFields.removeChild(contentFields.lastChild);
            }

            // Update title placeholder
            switch (type) {
                case 'text':
                    titleInput.placeholder = 'Заглавие (по желание)';
                    contentFields.innerHTML += `
                        <textarea class="content-content" name="elements[${index}][content]" placeholder="Въведете текст">${currentContent}</textarea>
                    `;
                    break;
                case 'image':
                    titleInput.placeholder = 'Заглавие на изображението (по желание)';
                    contentFields.innerHTML += `
                        <input type="url" class="content-content" name="elements[${index}][content]" placeholder="URL на изображението" value="${currentContent}">
                    `;
                    break;
                case 'image_text':
                    titleInput.placeholder = 'Заглавие (по желание)';
                    contentFields.innerHTML += `
                        <div class="image-text-fields">
                            <div class="image-field">
                                <label>Изображение:</label>
                                <input type="url" class="content-content" name="elements[${index}][content]" placeholder="URL на изображението" value="${currentContent}">
                            </div>
                            <div class="text-field">
                                <label>Текст:</label>
                                <textarea class="content-text" name="elements[${index}][text]" placeholder="Въведете текст">${currentText}</textarea>
                            </div>
                        </div>
                    `;
                    break;
                case 'image_list':
                    titleInput.placeholder = 'Заглавие (по желание)';
                    contentFields.innerHTML += `
                        <div class="image-text-fields">
                            <div class="image-field">
                                <label>Изображение:</label>
                                <input type="url" class="content-content" name="elements[${index}][content]" placeholder="URL на изображението" value="${currentContent}">
                            </div>
                            <div class="text-field">
                                <label>Списък:</label>
                                <textarea class="content-text" name="elements[${index}][text]" placeholder="Въведете всеки елемент на нов ред">${currentText}</textarea>
                            </div>
                        </div>
                    `;
                    break;
                case 'list':
                    titleInput.placeholder = 'Заглавие на списъка (по желание)';
                    contentFields.innerHTML += `
                        <textarea class="content-content" name="elements[${index}][content]" placeholder="Въведете всеки елемент на нов ред">${currentContent}</textarea>
                    `;
                    break;
                case 'quote':
                    titleInput.placeholder = 'Автор на цитата (по желание)';
                    contentFields.innerHTML += `
                        <textarea class="content-content" name="elements[${index}][content]" placeholder="Въведете цитата">${currentContent}</textarea>
                    `;
                    break;
            }

            // Add event listeners for content changes
            contentFields.querySelectorAll('input, textarea').forEach(input => {
                input.addEventListener('input', updatePreview);
            });
        }

        function updateLayout() {
            const layout = layoutSelect.value;
            contentElements.innerHTML = '';

            let numElements = 0;
            switch (layout) {
                case 'full':
                    numElements = 1;
                    break;
                case 'two-columns':
                case 'two-rows':
                    numElements = 2;
                    break;
                case 'three-columns':
                case 'three-rows':
                    numElements = 3;
                    break;
                case 'grid-2x2':
                    numElements = 4;
                    break;
                case 'grid-2x3':
                    numElements = 6;
                    break;
                case 'grid-2x4':
                    numElements = 8;
                    break;
                case 'grid-3x2':
                    numElements = 6;
                    break;
                case 'grid-3x3':
                    numElements = 9;
                    break;
                case 'grid-4x2':
                    numElements = 8;
                    break;
            }

            for (let i = 0; i < numElements; i++) {
                const element = createContentElement(i);
                contentElements.appendChild(element);
            }

            updatePreview();
        }

        function updatePreview() {
            const title = titleInput.value;
            const layout = layoutSelect.value;
            const elements = [];

            contentElements.querySelectorAll('.content-element').forEach((element, index) => {
                const typeSelect = element.querySelector('.content-type');
                const titleInput = element.querySelector('.content-title');
                const contentInput = element.querySelector('.content-content');
                const textTextarea = element.querySelector('.content-text');
                const styleInput = element.querySelector(`input[name="elements[${index}][style]"]`);

                if (!typeSelect || !titleInput || !contentInput || !styleInput) {
                    console.warn('Missing required elements for preview');
                    return;
                }

                const type = typeSelect.value;
                const title = titleInput.value;
                const content = contentInput.value;
                const text = textTextarea ? textTextarea.value : '';
                const style = JSON.parse(styleInput.value || '{}');

                elements.push({
                    type,
                    title,
                    content,
                    text,
                    style
                });
            });

            // Create preview HTML
            let previewHtml = `
                <div class="slide">
                    <h2>${escapeHtml(title)}</h2>
                    <div class="slide-content ${layout}">
            `;

            elements.forEach(element => {
                const styleString = Object.entries(element.style)
                    .map(([key, value]) => `${key}: ${value}`)
                    .join('; ');

                switch (element.type) {
                    case 'text':
                        previewHtml += `
                            <div class="element text" style="${styleString}">
                                ${element.title ? `<h3>${escapeHtml(element.title)}</h3>` : ''}
                                <div>${escapeHtml(element.content).replace(/\n/g, '<br>')}</div>
                            </div>
                        `;
                        break;
                    case 'image':
                        previewHtml += `
                            <div class="element image" style="${styleString}">
                                ${element.title ? `<h3>${escapeHtml(element.title)}</h3>` : ''}
                                <div class="image-container" style="background-image: url('${escapeHtml(element.content)}');"></div>
                            </div>
                        `;
                        break;
                    case 'image_text':
                        previewHtml += `
                            <div class="element image-text" style="${styleString}">
                                ${element.title ? `<h3>${escapeHtml(element.title)}</h3>` : ''}
                                <div class="image-text-container">
                                    <div class="image-container" style="background-image: url('${escapeHtml(element.content)}');"></div>
                                    <div class="text">${escapeHtml(element.text).replace(/\n/g, '<br>')}</div>
                                </div>
                            </div>
                        `;
                        break;
                    case 'image_list':
                        previewHtml += `
                            <div class="element image-list" style="${styleString}">
                                ${element.title ? `<h3>${escapeHtml(element.title)}</h3>` : ''}
                                <div class="image-list-container">
                                    <img src="${escapeHtml(element.content)}" alt="${escapeHtml(element.title)}">
                                    <ul>
                                        ${element.text.split('\n').map(item => `<li>${escapeHtml(item)}</li>`).join('')}
                                    </ul>
                                </div>
                            </div>
                        `;
                        break;
                    case 'list':
                        previewHtml += `
                            <div class="element list" style="${styleString}">
                                ${element.title ? `<h3>${escapeHtml(element.title)}</h3>` : ''}
                                <ul>
                                    ${element.content.split('\n').map(item => `<li>${escapeHtml(item)}</li>`).join('')}
                                </ul>
                            </div>
                        `;
                        break;
                    case 'quote':
                        previewHtml += `
                            <div class="element quote" style="${styleString}">
                                <blockquote>${escapeHtml(element.content)}</blockquote>
                                ${element.title ? `<cite>${escapeHtml(element.title)}</cite>` : ''}
                            </div>
                        `;
                        break;
                }
            });

            previewHtml += `
                    </div>
                </div>
            `;

            slidePreview.innerHTML = previewHtml;
        }
    </script>
</body>
</html> 