<?php
require_once __DIR__ . '/../../helpers/SlideRenderer.php';
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Създаване на слайд</title>
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/main.css">
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/slides.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Създаване на слайд</h1>
            
            <?php if (isset($data['error'])): ?>
                <div class="error"><?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <div class="editor-preview-container">
                <div class="editor-section">
                    <form action="<?= BASE_URL ?>/slides/create/<?= $data['presentation']['id'] ?>" method="POST">
                        <input type="hidden" name="presentation_id" value="<?= $data['presentation']['id'] ?>">
                        
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

                        <button type="button" onclick="addContentElement()" class="btn">Добави елемент</button>
                        <button type="submit" class="btn">Създай слайд</button>
                        <a href="<?= BASE_URL ?>/presentation/viewPresentation/<?= $data['presentation']['id'] ?>" class="btn btn-secondary">Отказ</a>
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

        function createContentElement(index, type, title = '', content = '', text = '') {
            const element = document.createElement('div');
            element.className = 'content-element';
            element.innerHTML = `
                <h4>Елемент ${index + 1}</h4>
                <select class="content-type" name="content_type_${index}" onchange="updateContentFields(this.parentElement, this.value)">
                    <option value="text">Текст</option>
                    <option value="image">Изображение</option>
                    <option value="image_text">Изображение и текст</option>
                    <option value="image_list">Изображение и списък</option>
                    <option value="list">Списък</option>
                    <option value="quote">Цитат</option>
                </select>
                <div class="content-fields">
                    <input type="text" class="content-title" name="content_title_${index}" placeholder="Заглавие (по желание)" value="${escapeHtml(title)}">
                    ${type === 'image_text' ? `
                        <div class="image-text-fields">
                            <div class="image-field">
                                <label>Изображение:</label>
                                <input type="url" class="content-content" name="content_content_${index}" placeholder="URL на изображението" value="${escapeHtml(content)}">
                            </div>
                            <div class="text-field">
                                <label>Текст:</label>
                                <textarea class="content-text" name="content_text_${index}" placeholder="Въведете текст">${escapeHtml(text)}</textarea>
                            </div>
                        </div>
                    ` : type === 'image' ? `
                        <input type="url" class="content-content" name="content_content_${index}" placeholder="URL на изображението" value="${escapeHtml(content)}">
                    ` : `
                        <textarea class="content-content" name="content_content_${index}" placeholder="Съдържание">${escapeHtml(content)}</textarea>
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

        function updateContentFields(element, type) {
            const contentFields = element.querySelector('.content-fields');
            const titleInput = element.querySelector('.content-title');
            const contentInput = element.querySelector('.content-content');
            const textTextarea = element.querySelector('.content-text');

            // Store current values
            const currentTitle = titleInput.value;
            const currentContent = contentInput ? contentInput.value : '';
            const currentText = textTextarea ? textTextarea.value : '';

            // Clear existing fields except title
            while (contentFields.children.length > 1) {
                contentFields.removeChild(contentFields.lastChild);
            }

            // Update title placeholder
            switch (type) {
                case 'text':
                    titleInput.placeholder = 'Заглавие (по желание)';
                    contentFields.innerHTML += `
                        <textarea class="content-content" name="${contentInput.name}" placeholder="Въведете текст">${currentContent}</textarea>
                    `;
                    break;
                case 'image':
                    titleInput.placeholder = 'Заглавие на изображението (по желание)';
                    contentFields.innerHTML += `
                        <input type="url" class="content-content" name="${contentInput.name}" placeholder="URL на изображението" value="${currentContent}">
                    `;
                    break;
                case 'image_text':
                    titleInput.placeholder = 'Заглавие (по желание)';
                    contentFields.innerHTML += `
                        <div class="image-text-fields">
                            <div class="image-field">
                                <label>Изображение:</label>
                                <input type="url" class="content-content" name="${contentInput.name}" placeholder="URL на изображението" value="${currentContent}">
                            </div>
                            <div class="text-field">
                                <label>Текст:</label>
                                <textarea class="content-text" name="content_text_${element.querySelector('.content-type').name.split('_')[2]}" placeholder="Въведете текст">${currentText}</textarea>
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
                                <input type="url" class="content-content" name="${contentInput.name}" placeholder="URL на изображението" value="${currentContent}">
                            </div>
                            <div class="text-field">
                                <label>Списък:</label>
                                <textarea class="content-text" name="content_text_${element.querySelector('.content-type').name.split('_')[2]}" placeholder="Въведете всеки елемент на нов ред">${currentText}</textarea>
                            </div>
                        </div>
                    `;
                    break;
                case 'list':
                    titleInput.placeholder = 'Заглавие на списъка (по желание)';
                    contentFields.innerHTML += `
                        <textarea class="content-content" name="${contentInput.name}" placeholder="Въведете всеки елемент на нов ред">${currentContent}</textarea>
                    `;
                    break;
                case 'quote':
                    titleInput.placeholder = 'Автор на цитата (по желание)';
                    contentFields.innerHTML += `
                        <textarea class="content-content" name="${contentInput.name}" placeholder="Въведете цитата">${currentContent}</textarea>
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
                    numElements = 2;
                    break;
                case 'three-columns':
                    numElements = 3;
                    break;
                case 'two-rows':
                    numElements = 2;
                    break;
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
                case 'grid-4x3':
                    numElements = 12;
                    break;
            }

            for (let i = 0; i < numElements; i++) {
                const element = createContentElement(i);
                contentElements.appendChild(element);
            }

            updatePreview();
        }

        function updatePreview() {
            const layout = layoutSelect.value;
            const title = titleInput.value;
            const elements = Array.from(contentElements.children).map(element => ({
                type: element.querySelector('.content-type').value,
                title: element.querySelector('.content-title').value,
                content: element.querySelector('.content-content').value,
                text: element.querySelector('.content-text')?.value || ''
            }));

            let previewHTML = `
                <div class="preview-slide">
                    <h2>${escapeHtml(title)}</h2>
                    <div class="layout-${layout}">
                        ${getLayoutHTML(layout, elements)}
                    </div>
                </div>
            `;

            slidePreview.innerHTML = previewHTML;
        }

        function getLayoutHTML(layout, elements) {
            switch (layout) {
                case 'full':
                    return getContentHTML(elements[0]);
                
                case 'two-columns':
                    return `
                        <div class="two-columns">
                            <div class="column">${getContentHTML(elements[0])}</div>
                            <div class="column">${getContentHTML(elements[1])}</div>
                        </div>
                    `;
                
                case 'three-columns':
                    return `
                        <div class="three-columns">
                            <div class="column">${getContentHTML(elements[0])}</div>
                            <div class="column">${getContentHTML(elements[1])}</div>
                            <div class="column">${getContentHTML(elements[2])}</div>
                        </div>
                    `;
                case 'two-rows':
                    return `
                        <div class="two-rows">
                            <div class="column">${getContentHTML(elements[0])}</div>
                            <div class="column">${getContentHTML(elements[1])}</div>
                        </div>
                    `;
                
                case 'three-rows':
                    return `
                        <div class="three-rows">
                            <div class="column">${getContentHTML(elements[0])}</div>
                            <div class="column">${getContentHTML(elements[1])}</div>
                            <div class="column">${getContentHTML(elements[2])}</div>
                        </div>
                    `;
                
                case 'grid-2x2':
                    return `
                        <div class="grid-2x2">
                            ${elements.map((element, i) => `
                                <div class="cell">${getContentHTML(element)}</div>
                            `).join('')}
                        </div>
                    `;
                case 'grid-2x3':
                    return `
                        <div class="grid-2x3">
                            ${elements.map((element, i) => `
                                <div class="cell">${getContentHTML(element)}</div>
                            `).join('')}
                        </div>
                    `;
                case 'grid-3x2':
                    return `
                        <div class="grid-3x2">
                            ${elements.map((element, i) => `
                                <div class="cell">${getContentHTML(element)}</div>
                            `).join('')}
                        </div>
                    `;
                case 'grid-3x3':
                    return `
                        <div class="grid-3x3">
                            ${elements.map((element, i) => `
                                <div class="cell">${getContentHTML(element)}</div>
                            `).join('')}
                        </div>
                    `;
                case 'grid-4x2':
                    return `
                        <div class="grid-4x2">
                            ${elements.map((element, i) => `
                                <div class="cell">${getContentHTML(element)}</div>
                            `).join('')}
                        </div>
                    `;
                case 'grid-4x3':
                    return `
                        <div class="grid-4x3">
                            ${elements.map((element, i) => `
                                <div class="cell">${getContentHTML(element)}</div>
                            `).join('')}
                        </div>
                    `;
            }
        }

        function getContentHTML(element) {
            if (!element) return '';

            switch (element.type) {
                case 'text':
                    return `
                        <div class="content-text">
                            ${element.title ? `<h3>${escapeHtml(element.title)}</h3>` : ''}
                            <div class="text-content">${formatContent(element.content)}</div>
                        </div>
                    `;
                
                case 'image':
                    return `
                        <div class="content-image">
                            ${element.title ? `<h3>${escapeHtml(element.title)}</h3>` : ''}
                            <img src="${escapeHtml(element.content)}" alt="${escapeHtml(element.title)}" onerror="this.style.display='none'">
                        </div>
                    `;
                
                case 'image_text':
                    return `
                        <div class="content-image-text">
                            ${element.title ? `<h3>${escapeHtml(element.title)}</h3>` : ''}
                            <div class="image-text-container">
                                <img src="${escapeHtml(element.content)}" alt="${escapeHtml(element.title)}" onerror="this.style.display='none'">
                                ${element.text ? `<div class="image-text">${formatContent(element.text)}</div>` : ''}
                            </div>
                        </div>
                    `;
                
                case 'image_list':
                    const listItems = element.text ? element.text.split('\n').filter(item => item.trim() !== '') : [];
                    return `
                        <div class="content-image-list">
                            ${element.title ? `<h3>${escapeHtml(element.title)}</h3>` : ''}
                            <div class="image-list-container">
                                <img src="${escapeHtml(element.content)}" alt="${escapeHtml(element.title)}" onerror="this.style.display='none'">
                                ${listItems.length > 0 ? `
                                    <div class="image-list">
                                        <ul>
                                            ${listItems.map(item => `<li>${escapeHtml(item.trim())}</li>`).join('')}
                                        </ul>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                
                case 'list':
                    const items = element.content.split('\n').filter(item => item.trim() !== '');
                    return `
                        <div class="content-list">
                            ${element.title ? `<h3>${escapeHtml(element.title)}</h3>` : ''}
                            <ul>
                                ${items.map(item => `<li>${escapeHtml(item.trim())}</li>`).join('')}
                            </ul>
                        </div>
                    `;
                
                case 'quote':
                    return `
                        <div class="content-quote">
                            <blockquote>${formatContent(element.content)}</blockquote>
                            ${element.title ? `<cite>— ${escapeHtml(element.title)}</cite>` : ''}
                        </div>
                    `;
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatContent(content) {
            return content
                .split('\n')
                .map(line => `<p>${escapeHtml(line)}</p>`)
                .join('');
        }

        // Add event listeners
        layoutSelect.addEventListener('change', updateLayout);
        titleInput.addEventListener('input', updatePreview);

        // Initialize layout
        updateLayout();
    </script>
</body>
</html> 