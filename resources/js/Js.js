 document.addEventListener('DOMContentLoaded', function() {
                    // Inizializza lo swiper solo se la struttura esiste nella pagina
                    if (document.querySelector('.mySwiper')) {
                        var swiper = new Swiper(".mySwiper", {
                            slidesPerView: 1,
                            spaceBetween: 30,
                            loop: true,
                            autoplay: { 
                                delay: 1700, 
                                disableOnInteraction: false 
                            },
                            pagination: { 
                                el: ".swiper-pagination", 
                                clickable: true 
                            },
                            navigation: { 
                                nextEl: ".swiper-button-next", 
                                prevEl: ".swiper-button-prev" 
                            },
                        });
                    }
                });



                  document.addEventListener('DOMContentLoaded', function() {
                    const hamburger = document.getElementById('hamburger');
                    const menuUl = document.querySelector('.filter-menu ul');
                    
                    if (hamburger && menuUl) {
                        hamburger.addEventListener('click', () => {
                            hamburger.classList.toggle('open');
                            menuUl.classList.toggle('show');
                        });
                    }
                });