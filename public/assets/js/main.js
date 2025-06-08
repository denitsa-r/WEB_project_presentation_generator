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

// Инициализираме drag and drop функционалността когато DOM е зареден
document.addEventListener('DOMContentLoaded', function() {
    initializeDragAndDrop();
}); 