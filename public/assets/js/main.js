// Функция за инициализиране на drag and drop функционалността
function initializeDragAndDrop() {
    const slides = document.querySelectorAll('.slide');
    let draggedSlide = null;
    let originalOrder = [];

    // Запазваме оригиналния ред на слайдовете
    slides.forEach(slide => {
        originalOrder.push(slide.dataset.slideId);
    });

    slides.forEach(slide => {
        slide.addEventListener('dragstart', function(e) {
            draggedSlide = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.dataset.slideId);
        });

        slide.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            draggedSlide = null;
        });

        slide.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            
            const rect = this.getBoundingClientRect();
            const midY = rect.top + rect.height / 2;
            
            if (e.clientY < midY) {
                this.classList.add('drag-over-top');
                this.classList.remove('drag-over-bottom');
            } else {
                this.classList.add('drag-over-bottom');
                this.classList.remove('drag-over-top');
            }
        });

        slide.addEventListener('dragleave', function() {
            this.classList.remove('drag-over-top', 'drag-over-bottom');
        });

        slide.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over-top', 'drag-over-bottom');
            
            if (draggedSlide === this) return;

            const slidesContainer = document.querySelector('.slides-container');
            const rect = this.getBoundingClientRect();
            const midY = rect.top + rect.height / 2;
            
            if (e.clientY < midY) {
                slidesContainer.insertBefore(draggedSlide, this);
            } else {
                slidesContainer.insertBefore(draggedSlide, this.nextSibling);
            }

            // Обновяваме реда на слайдовете
            const newOrder = Array.from(slidesContainer.querySelectorAll('.slide')).map(slide => slide.dataset.slideId);
            
            console.log('Sending new order:', newOrder);
            
            // Изпращаме AJAX заявка за обновяване на реда
            fetch(window.location.origin + '/WEB_project_presentation_generator/public/slides/updateOrder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    slides: newOrder
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (!data.success) {
                    console.error('Failed to update slide order:', data.message);
                    // Връщаме слайдовете в оригиналния ред при грешка
                    const slidesArray = Array.from(slidesContainer.querySelectorAll('.slide'));
                    originalOrder.forEach((slideId, index) => {
                        const slide = slidesArray.find(s => s.dataset.slideId === slideId);
                        if (slide) {
                            slidesContainer.appendChild(slide);
                        }
                    });
                } else {
                    console.log('Successfully updated slide order');
                }
            })
            .catch(error => {
                console.error('Error updating slide order:', error);
                // Връщаме слайдовете в оригиналния ред при грешка
                const slidesArray = Array.from(slidesContainer.querySelectorAll('.slide'));
                originalOrder.forEach((slideId, index) => {
                    const slide = slidesArray.find(s => s.dataset.slideId === slideId);
                    if (slide) {
                        slidesContainer.appendChild(slide);
                    }
                });
            });
        });
    });
}

function moveSlide(slideId, direction) {
    const slidesContainer = document.querySelector('.slides-container');
    const slides = Array.from(slidesContainer.children);
    const currentSlideContainer = slides.find(container => 
        container.querySelector('.slide').dataset.slideId === slideId.toString()
    );
    const currentIndex = slides.indexOf(currentSlideContainer);
    
    if (direction === 'up' && currentIndex > 0) {
        // Разменяме слайдовете в DOM
        const previousSlideContainer = slides[currentIndex - 1];
        slidesContainer.insertBefore(currentSlideContainer, previousSlideContainer);
        
        // Обновяваме реда в базата данни
        const newOrder = Array.from(slidesContainer.querySelectorAll('.slide')).map((slide, index) => ({
            id: slide.dataset.slideId,
            order: index + 1
        }));
        
        updateSlideOrder(newOrder);
    } else if (direction === 'down' && currentIndex < slides.length - 1) {
        // Разменяме слайдовете в DOM
        const nextSlideContainer = slides[currentIndex + 1];
        slidesContainer.insertBefore(nextSlideContainer, currentSlideContainer);
        
        // Обновяваме реда в базата данни
        const newOrder = Array.from(slidesContainer.querySelectorAll('.slide')).map((slide, index) => ({
            id: slide.dataset.slideId,
            order: index + 1
        }));
        
        updateSlideOrder(newOrder);
    }
}

function updateSlideOrder(slideOrder) {
    console.log('Sending new order:', slideOrder);
    
    fetch(window.location.origin + '/WEB_project_presentation_generator/public/slides/updateOrder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ slides: slideOrder })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            console.log('Successfully updated slide order');
        } else {
            console.error('Failed to update slide order:', data.message);
            // При грешка презареждаме страницата
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error updating slide order:', error);
        // При грешка презареждаме страницата
        window.location.reload();
    });
}

// Премахваме стария drag and drop код
document.addEventListener('DOMContentLoaded', function() {
    // Премахваме draggable атрибутите
    document.querySelectorAll('.slide').forEach(slide => {
        slide.removeAttribute('draggable');
    });
}); 