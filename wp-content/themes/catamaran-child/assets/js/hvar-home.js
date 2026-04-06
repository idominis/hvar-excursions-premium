(function () {
  var ready = function (fn) {
    if (document.readyState !== 'loading') {
      fn();
    } else {
      document.addEventListener('DOMContentLoaded', fn);
    }
  };

  ready(function () {
    var menuToggle = document.querySelector('.hex-nav__toggle');
    var menu = document.querySelector('.hex-nav__menu');
    var searchToggle = document.querySelector('.hex-search-toggle');
    var searchDrawer = document.getElementById('hex-search-drawer');
    var searchInput = document.getElementById('hex-search-input');
    var panelToggle = document.querySelector('.hex-panel-toggle');
    var panel = document.getElementById('hex-sidepanel');
    var panelClose = document.querySelector('.hex-sidepanel__close');
    var panelOverlay = document.querySelector('.hex-sidepanel-overlay');
    var backToTop = document.querySelector('.hex-back-to-top');
    var heroSlider = document.querySelector('[data-hex-hero-slider]');
    var heroSlides = heroSlider ? heroSlider.querySelectorAll('.hex-hero-slide') : [];
    var heroCopies = document.querySelectorAll('[data-hex-hero-copy]');
    var heroDots = document.querySelectorAll('[data-hex-hero-dot]');
    var heroPrevArrow = document.querySelector('[data-hex-hero-arrow="prev"]');
    var heroNextArrow = document.querySelector('[data-hex-hero-arrow="next"]');
    var destinationTyped = document.querySelector('[data-hex-destination-typed]');
    var serviceGrid = document.querySelector('.hex-services__grid');
    var serviceCards = serviceGrid ? Array.prototype.slice.call(serviceGrid.querySelectorAll('.hex-card')) : [];
    var serviceOrder = [0, 1, 2];
    var serviceSwapRight = true;
    var serviceTimer = null;
    var serviceAnimating = false;
    var destinationSwiperEl = document.querySelector('[data-hex-destination-swiper]');
    var experienceVideoTrigger = document.querySelector('.hex-experience__video');
    var videoModal = document.querySelector('[data-hex-video-modal]');
    var videoFrame = videoModal ? videoModal.querySelector('[data-hex-video-frame]') : null;
    var videoCloseControls = videoModal ? videoModal.querySelectorAll('[data-hex-video-close]') : [];
    var rentalGalleries = Array.prototype.slice.call(document.querySelectorAll('[data-hex-gallery]'));
    var galleryModal = document.querySelector('[data-hex-gallery-modal]');
    var galleryModalImage = galleryModal ? galleryModal.querySelector('[data-hex-gallery-modal-image]') : null;
    var galleryModalTitle = galleryModal ? galleryModal.querySelector('[data-hex-gallery-modal-title]') : null;
    var galleryModalCounter = galleryModal ? galleryModal.querySelector('[data-hex-gallery-modal-counter]') : null;
    var galleryCloseControls = galleryModal ? galleryModal.querySelectorAll('[data-hex-gallery-close]') : [];
    var galleryPrevButton = galleryModal ? galleryModal.querySelector('[data-hex-gallery-prev]') : null;
    var galleryNextButton = galleryModal ? galleryModal.querySelector('[data-hex-gallery-next]') : null;
    var rentalRevealToggles = Array.prototype.slice.call(document.querySelectorAll('[data-hex-reveal-toggle]'));
    var transferRoot = document.querySelector('[data-hex-transfer-root]');
    var galleryState = {
      images: [],
      index: 0,
      title: '',
      opener: null
    };
    var lastFocusedBeforeVideo = null;
    var heroIndex = 0;
    var heroTimer = null;
    var heroDelay = 6200;
    var closeGalleryModal = function () {
      if (!galleryModal || galleryModal.hasAttribute('hidden')) {
        return;
      }

      galleryModal.setAttribute('hidden', '');
      galleryModal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('hex-gallery-open');

      if (galleryModalImage) {
        galleryModalImage.setAttribute('src', '');
        galleryModalImage.setAttribute('alt', '');
      }

      if (galleryState.opener && typeof galleryState.opener.focus === 'function') {
        galleryState.opener.focus();
      }

      galleryState.images = [];
      galleryState.index = 0;
      galleryState.title = '';
      galleryState.opener = null;
    };

    var updateGalleryModal = function () {
      if (!galleryModal || !galleryModalImage || !galleryState.images.length) {
        return;
      }

      var safeIndex = ((galleryState.index % galleryState.images.length) + galleryState.images.length) % galleryState.images.length;
      galleryState.index = safeIndex;

      galleryModalImage.setAttribute('src', galleryState.images[safeIndex]);
      galleryModalImage.setAttribute('alt', galleryState.title + ' photo ' + (safeIndex + 1));

      if (galleryModalTitle) {
        galleryModalTitle.textContent = galleryState.title;
      }

      if (galleryModalCounter) {
        galleryModalCounter.textContent = (safeIndex + 1) + ' / ' + galleryState.images.length;
      }
    };

    var openGalleryModal = function (images, index, title, opener) {
      if (!galleryModal || !galleryModalImage || !images.length) {
        return;
      }

      galleryState.images = images.slice();
      galleryState.index = index || 0;
      galleryState.title = title || 'Boat gallery';
      galleryState.opener = opener || null;

      updateGalleryModal();
      galleryModal.removeAttribute('hidden');
      galleryModal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('hex-gallery-open');

      if (galleryPrevButton) {
        galleryPrevButton.focus();
      }
    };

    var setGalleryImage = function (gallery, nextIndex) {
      if (!gallery) {
        return;
      }

      var images = [];
      var rawImages = gallery.getAttribute('data-hex-gallery-images');

      try {
        images = rawImages ? JSON.parse(rawImages) : [];
      } catch (e) {
        images = [];
      }

      if (!images.length) {
        return;
      }

      var safeIndex = ((nextIndex % images.length) + images.length) % images.length;
      var mainImage = gallery.querySelector('[data-hex-gallery-main]');
      var thumbs = gallery.querySelectorAll('[data-hex-gallery-thumb]');

      gallery.setAttribute('data-hex-gallery-current', String(safeIndex));

      if (mainImage) {
        mainImage.setAttribute('src', images[safeIndex]);
      }

      thumbs.forEach(function (thumb) {
        var thumbIndex = parseInt(thumb.getAttribute('data-hex-gallery-thumb'), 10);
        thumb.classList.toggle('is-active', thumbIndex === safeIndex);
      });
    };

    var closeMenu = function () {
      if (!menu || !menuToggle) {
        return;
      }
      menu.classList.remove('is-open');
      menuToggle.setAttribute('aria-expanded', 'false');
    };

    var closeSearch = function () {
      if (!searchDrawer) {
        return;
      }
      searchDrawer.setAttribute('hidden', '');
      if (searchToggle) {
        searchToggle.setAttribute('aria-expanded', 'false');
      }
    };

    var openSearch = function () {
      if (!searchDrawer) {
        return;
      }
      searchDrawer.removeAttribute('hidden');
      if (searchToggle) {
        searchToggle.setAttribute('aria-expanded', 'true');
      }
      if (searchInput) {
        searchInput.focus();
      }
    };

    var closePanel = function () {
      if (!panel || !panelOverlay) {
        return;
      }
      panel.setAttribute('hidden', '');
      panelOverlay.setAttribute('hidden', '');
      if (panelToggle) {
        panelToggle.setAttribute('aria-expanded', 'false');
      }
      document.body.style.overflow = '';
    };

    var openPanel = function () {
      if (!panel || !panelOverlay) {
        return;
      }
      panel.removeAttribute('hidden');
      panelOverlay.removeAttribute('hidden');
      if (panelToggle) {
        panelToggle.setAttribute('aria-expanded', 'true');
      }
      document.body.style.overflow = 'hidden';
    };

    var toEmbedVideoUrl = function (url) {
      if (!url) {
        return '';
      }

      try {
        var parsed = new URL(url, window.location.origin);
        var hostname = parsed.hostname.replace(/^www\./, '');
        var videoId = '';

        if (hostname === 'youtu.be') {
          videoId = parsed.pathname.split('/').filter(Boolean)[0] || '';
        } else if (hostname.indexOf('youtube.com') !== -1) {
          if (parsed.pathname === '/watch') {
            videoId = parsed.searchParams.get('v') || '';
          } else if (parsed.pathname.indexOf('/embed/') === 0) {
            videoId = parsed.pathname.replace('/embed/', '').split('/')[0];
          } else if (parsed.pathname.indexOf('/shorts/') === 0) {
            videoId = parsed.pathname.replace('/shorts/', '').split('/')[0];
          }
        }

        if (videoId) {
          return 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0';
        }
      } catch (e) {
        return url;
      }

      return url;
    };

    var closeVideoModal = function () {
      if (!videoModal || videoModal.hasAttribute('hidden')) {
        return;
      }

      videoModal.setAttribute('hidden', '');
      videoModal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('hex-video-open');

      if (videoFrame) {
        videoFrame.setAttribute('src', '');
      }

      if (lastFocusedBeforeVideo && typeof lastFocusedBeforeVideo.focus === 'function') {
        lastFocusedBeforeVideo.focus();
      }
      lastFocusedBeforeVideo = null;
    };

    var openVideoModal = function () {
      if (!experienceVideoTrigger || !videoModal || !videoFrame) {
        return;
      }

      var sourceUrl = experienceVideoTrigger.getAttribute('href') || '';
      var embedUrl = toEmbedVideoUrl(sourceUrl);
      if (!embedUrl) {
        return;
      }

      lastFocusedBeforeVideo = document.activeElement;
      videoFrame.setAttribute('src', embedUrl);
      videoModal.removeAttribute('hidden');
      videoModal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('hex-video-open');

      var closeButton = videoModal.querySelector('.hex-video-modal__close');
      if (closeButton) {
        closeButton.focus();
      }
    };

    if (menuToggle && menu) {
      menuToggle.addEventListener('click', function () {
        var isOpen = menu.classList.toggle('is-open');
        menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      });

      menu.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', closeMenu);
      });
    }

    if (searchToggle && searchDrawer) {
      searchToggle.addEventListener('click', function () {
        var isOpen = !searchDrawer.hasAttribute('hidden');
        if (isOpen) {
          closeSearch();
        } else {
          closePanel();
          openSearch();
        }
      });
    }

    if (panelToggle && panel && panelOverlay) {
      panelToggle.addEventListener('click', function () {
        var isOpen = !panel.hasAttribute('hidden');
        if (isOpen) {
          closePanel();
        } else {
          closeSearch();
          openPanel();
        }
      });
    }

    if (panelClose) {
      panelClose.addEventListener('click', closePanel);
    }

    if (panelOverlay) {
      panelOverlay.addEventListener('click', closePanel);
    }

    if (experienceVideoTrigger && videoModal && videoFrame) {
      experienceVideoTrigger.addEventListener('click', function (event) {
        event.preventDefault();
        openVideoModal();
      });

      videoCloseControls.forEach(function (control) {
        control.addEventListener('click', function (event) {
          event.preventDefault();
          closeVideoModal();
        });
      });
    }

    document.addEventListener('click', function (event) {
      if (menu && menuToggle && menu.classList.contains('is-open')) {
        if (!menu.contains(event.target) && !menuToggle.contains(event.target)) {
          closeMenu();
        }
      }

      if (!searchDrawer || searchDrawer.hasAttribute('hidden')) {
        return;
      }
      if (searchDrawer.contains(event.target)) {
        return;
      }
      if (searchToggle && searchToggle.contains(event.target)) {
        return;
      }
      closeSearch();
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeMenu();
        closeSearch();
        closePanel();
        closeVideoModal();
        closeGalleryModal();
      }
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 800) {
        closeMenu();
      }
    });

    var serviceMotionReduced = function () {
      return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    };

    var serviceAnimationEnabled = function () {
      return serviceCards.length === 3 && window.innerWidth > 1140 && !serviceMotionReduced();
    };

    var setServiceOrder = function (orderMap) {
      orderMap.forEach(function (cardIndex, slotIndex) {
        if (serviceCards[cardIndex]) {
          serviceCards[cardIndex].style.order = String(slotIndex + 1);
        }
      });
    };

    var resetServiceOrder = function () {
      serviceOrder = [0, 1, 2];
      serviceSwapRight = true;
      setServiceOrder(serviceOrder);
    };

    var animateServiceOrder = function (nextOrder) {
      if (serviceAnimating) {
        return;
      }

      serviceAnimating = true;
      var firstRects = serviceCards.map(function (card) {
        return card.getBoundingClientRect();
      });

      setServiceOrder(nextOrder);

      var lastRects = serviceCards.map(function (card) {
        return card.getBoundingClientRect();
      });

      serviceCards.forEach(function (card, index) {
        var dx = firstRects[index].left - lastRects[index].left;
        var dy = firstRects[index].top - lastRects[index].top;

        card.style.transition = 'none';
        card.style.transform = 'translate(' + dx + 'px, ' + dy + 'px)';
        card.getBoundingClientRect();

        window.requestAnimationFrame(function () {
          card.style.transition = 'transform 780ms cubic-bezier(0.22, 0.61, 0.36, 1)';
          card.style.transform = 'translate(0, 0)';
        });
      });

      window.setTimeout(function () {
        serviceCards.forEach(function (card) {
          card.style.transition = '';
          card.style.transform = '';
        });
        serviceAnimating = false;
      }, 860);
    };

    var tickServiceCarousel = function () {
      var nextOrder = serviceOrder.slice();
      var temp = null;

      if (serviceSwapRight) {
        temp = nextOrder[1];
        nextOrder[1] = nextOrder[2];
        nextOrder[2] = temp;
      } else {
        temp = nextOrder[1];
        nextOrder[1] = nextOrder[0];
        nextOrder[0] = temp;
      }

      serviceSwapRight = !serviceSwapRight;
      animateServiceOrder(nextOrder);
      serviceOrder = nextOrder;
    };

    var stopServiceCarousel = function () {
      if (serviceTimer) {
        window.clearInterval(serviceTimer);
        serviceTimer = null;
      }
      serviceAnimating = false;
      serviceCards.forEach(function (card) {
        card.style.transition = '';
        card.style.transform = '';
      });
      resetServiceOrder();
    };

    var startServiceCarousel = function () {
      if (!serviceAnimationEnabled()) {
        stopServiceCarousel();
        return;
      }

      if (serviceTimer) {
        return;
      }

      resetServiceOrder();
      serviceTimer = window.setInterval(tickServiceCarousel, 4200);
    };

    if (heroSlides.length > 0 && heroCopies.length > 0) {
      var setHeroSlide = function (nextIndex) {
        var total = heroSlides.length;
        var safeIndex = ((nextIndex % total) + total) % total;

        heroSlides.forEach(function (slide, index) {
          slide.classList.remove('is-active');
          slide.classList.remove('is-exit');
          if (index === heroIndex && index !== safeIndex) {
            slide.classList.add('is-exit');
          }
        });

        heroCopies.forEach(function (copy) {
          copy.classList.remove('is-active');
        });

        heroDots.forEach(function (dot) {
          dot.classList.remove('is-active');
        });

        heroSlides[safeIndex].classList.add('is-active');
        if (heroCopies[safeIndex]) {
          heroCopies[safeIndex].classList.add('is-active');
        }
        if (heroDots[safeIndex]) {
          heroDots[safeIndex].classList.add('is-active');
        }

        heroIndex = safeIndex;
      };

      var restartHeroTimer = function () {
        if (heroTimer) {
          window.clearInterval(heroTimer);
        }
        heroTimer = window.setInterval(function () {
          setHeroSlide(heroIndex + 1);
        }, heroDelay);
      };

      heroDots.forEach(function (dot) {
        dot.addEventListener('click', function () {
          var nextIndex = parseInt(dot.getAttribute('data-hex-hero-dot'), 10);
          if (!Number.isNaN(nextIndex)) {
            setHeroSlide(nextIndex);
            restartHeroTimer();
          }
        });
      });

      if (heroPrevArrow) {
        heroPrevArrow.addEventListener('click', function () {
          setHeroSlide(heroIndex - 1);
          restartHeroTimer();
        });
      }

      if (heroNextArrow) {
        heroNextArrow.addEventListener('click', function () {
          setHeroSlide(heroIndex + 1);
          restartHeroTimer();
        });
      }

      setHeroSlide(0);
      restartHeroTimer();
    }

    if (serviceGrid && serviceCards.length === 3) {
      startServiceCarousel();

      window.addEventListener('resize', function () {
        if (serviceAnimationEnabled()) {
          startServiceCarousel();
        } else {
          stopServiceCarousel();
        }
      });
    }

    if (destinationTyped) {
      var typedWords = [];
      var typedData = destinationTyped.getAttribute('data-words');
      try {
        typedWords = typedData ? JSON.parse(typedData) : [];
      } catch (e) {
        typedWords = [];
      }

      if (typedWords.length > 1) {
        var typedWordIndex = 0;
        var typedCharIndex = 0;
        var typedPhase = 'typing'; // typing | hold | clear

        var stepTypedWord = function () {
          var currentWord = typedWords[typedWordIndex] || '';
          var delay = 90;

          if (typedPhase === 'typing') {
            typedCharIndex += 1;
            if (typedCharIndex > currentWord.length) {
              typedCharIndex = currentWord.length;
            }
            destinationTyped.textContent = currentWord.slice(0, typedCharIndex);

            if (typedCharIndex >= currentWord.length) {
              typedPhase = 'hold';
              delay = 1300;
            }
          } else if (typedPhase === 'hold') {
            destinationTyped.textContent = '';
            typedCharIndex = 0;
            typedWordIndex = (typedWordIndex + 1) % typedWords.length;
            typedPhase = 'typing';
            delay = 230;
          }

          window.setTimeout(stepTypedWord, delay);
        };

        destinationTyped.textContent = '';
        window.setTimeout(stepTypedWord, 420);
      }
    }

    if (destinationSwiperEl) {
      destinationSwiperEl.querySelectorAll('img').forEach(function (img) {
        img.setAttribute('draggable', 'false');
      });

      if (typeof window.Swiper !== 'undefined') {
        if (!destinationSwiperEl.swiper) {
          new window.Swiper(destinationSwiperEl, {
            slidesPerView: 'auto',
            spaceBetween: 60,
            speed: 600,
            loop: true,
            grabCursor: true,
            watchOverflow: false,
            allowTouchMove: true,
            autoplay: {
              delay: 7000,
              disableOnInteraction: false
            },
            breakpoints: {
              0: {
                spaceBetween: 16
              },
              768: {
                spaceBetween: 30
              },
              1025: {
                spaceBetween: 60
              }
            },
            on: {
              touchStart: function () {
                destinationSwiperEl.classList.add('is-dragging');
              },
              touchEnd: function () {
                destinationSwiperEl.classList.remove('is-dragging');
              },
              sliderFirstMove: function () {
                destinationSwiperEl.classList.add('is-dragging');
              },
              transitionEnd: function () {
                destinationSwiperEl.classList.remove('is-dragging');
              }
            }
          });
        }
      } else {
        // Fallback drag-scroll if Swiper is not available for any reason.
        var dragPointerDown = false;
        var dragStartX = 0;
        var dragStartScroll = 0;
        var dragMoved = false;
        var dragSuppressClick = false;

        destinationSwiperEl.addEventListener('pointerdown', function (event) {
          if (event.pointerType === 'mouse' && event.button !== 0) {
            return;
          }
          event.preventDefault();
          dragPointerDown = true;
          dragMoved = false;
          dragStartX = event.clientX;
          dragStartScroll = destinationSwiperEl.scrollLeft;
          destinationSwiperEl.classList.add('is-dragging');
          if (destinationSwiperEl.setPointerCapture) {
            destinationSwiperEl.setPointerCapture(event.pointerId);
          }
        });

        destinationSwiperEl.addEventListener('pointermove', function (event) {
          if (!dragPointerDown) {
            return;
          }
          event.preventDefault();
          var dx = event.clientX - dragStartX;
          if (Math.abs(dx) > 6) {
            dragMoved = true;
          }
          destinationSwiperEl.scrollLeft = dragStartScroll - dx;
        });

        var stopDrag = function () {
          if (!dragPointerDown) {
            return;
          }
          dragPointerDown = false;
          destinationSwiperEl.classList.remove('is-dragging');
          if (dragMoved) {
            dragSuppressClick = true;
            window.setTimeout(function () {
              dragSuppressClick = false;
            }, 80);
          }
        };

        destinationSwiperEl.addEventListener('pointerup', stopDrag);
        destinationSwiperEl.addEventListener('pointercancel', stopDrag);
        destinationSwiperEl.addEventListener('pointerleave', function (event) {
          if (event.pointerType === 'mouse') {
            stopDrag();
          }
        });

        destinationSwiperEl.querySelectorAll('a').forEach(function (link) {
          link.addEventListener('click', function (event) {
            if (dragSuppressClick || dragMoved) {
              event.preventDefault();
              event.stopPropagation();
            }
          });
        });
      }
    }

    if (backToTop) {
      var toggleBackToTop = function () {
        if (window.scrollY > 340) {
          backToTop.classList.add('is-visible');
        } else {
          backToTop.classList.remove('is-visible');
        }
      };

      toggleBackToTop();
      window.addEventListener('scroll', toggleBackToTop, { passive: true });

      backToTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }

    if (rentalGalleries.length) {
      rentalGalleries.forEach(function (gallery) {
        var images = [];
        var rawImages = gallery.getAttribute('data-hex-gallery-images');
        var title = gallery.getAttribute('data-hex-gallery-title') || 'Boat gallery';
        var mainTrigger = gallery.querySelector('[data-hex-gallery-open]');
        var thumbs = gallery.querySelectorAll('[data-hex-gallery-thumb]');

        try {
          images = rawImages ? JSON.parse(rawImages) : [];
        } catch (e) {
          images = [];
        }

        if (!images.length) {
          return;
        }

        setGalleryImage(gallery, 0);

        thumbs.forEach(function (thumb) {
          thumb.addEventListener('click', function () {
            var thumbIndex = parseInt(thumb.getAttribute('data-hex-gallery-thumb'), 10);
            if (!Number.isNaN(thumbIndex)) {
              setGalleryImage(gallery, thumbIndex);
            }
          });
        });

        if (mainTrigger) {
          mainTrigger.addEventListener('click', function () {
            var currentIndex = parseInt(gallery.getAttribute('data-hex-gallery-current') || '0', 10);
            openGalleryModal(images, Number.isNaN(currentIndex) ? 0 : currentIndex, title, mainTrigger);
          });
        }
      });
    }

    if (galleryModal) {
      galleryCloseControls.forEach(function (control) {
        control.addEventListener('click', function (event) {
          event.preventDefault();
          closeGalleryModal();
        });
      });

      if (galleryPrevButton) {
        galleryPrevButton.addEventListener('click', function () {
          galleryState.index -= 1;
          updateGalleryModal();
        });
      }

      if (galleryNextButton) {
        galleryNextButton.addEventListener('click', function () {
          galleryState.index += 1;
          updateGalleryModal();
        });
      }
    }

    if (rentalRevealToggles.length) {
      rentalRevealToggles.forEach(function (toggle) {
        var targetId = toggle.getAttribute('aria-controls');
        var targetGrid = targetId ? document.getElementById(targetId) : null;
        var label = toggle.querySelector('[data-hex-reveal-label]');

        if (!targetGrid) {
          return;
        }

        toggle.addEventListener('click', function () {
          var isExpanded = toggle.getAttribute('aria-expanded') === 'true';
          var nextState = !isExpanded;

          toggle.setAttribute('aria-expanded', nextState ? 'true' : 'false');
          targetGrid.classList.toggle('is-expanded', nextState);

          if (label) {
            label.textContent = nextState ? 'View Less' : 'View More';
          }
        });
      });
    }

    if (transferRoot) {
      var transferRoutes = {};
      var transferRoutesRaw = transferRoot.getAttribute('data-hex-transfer-routes');
      var transferFrom = transferRoot.querySelector('[data-hex-transfer-from]');
      var transferTo = transferRoot.querySelector('[data-hex-transfer-to]');
      var transferDate = transferRoot.querySelector('[data-hex-transfer-date]');
      var transferTime = transferRoot.querySelector('[data-hex-transfer-time]');
      var transferPassengers = transferRoot.querySelector('[data-hex-transfer-passengers]');
      var transferLuggage = transferRoot.querySelector('[data-hex-transfer-luggage]');
      var transferNotes = transferRoot.querySelector('[data-hex-transfer-notes]');
      var transferSwap = transferRoot.querySelector('[data-hex-transfer-swap]');
      var transferChips = transferRoot.querySelectorAll('[data-hex-transfer-route]');
      var transferSummaryRoute = transferRoot.querySelector('[data-hex-transfer-summary="route"]');
      var transferSummaryDuration = transferRoot.querySelector('[data-hex-transfer-summary="duration"]');
      var transferSummaryGroup = transferRoot.querySelector('[data-hex-transfer-summary="group"]');
      var transferSummaryTiming = transferRoot.querySelector('[data-hex-transfer-summary="timing"]');
      var transferSummaryNote = transferRoot.querySelector('[data-hex-transfer-summary="note"]');
      var transferSummaryStatus = transferRoot.querySelector('[data-hex-transfer-summary="status"]');
      var transferSummaryOffpeak = transferRoot.querySelector('[data-hex-transfer-summary="offpeak"]');
      var transferContact = transferRoot.querySelector('[data-hex-transfer-contact]');
      var transferWhatsapp = transferRoot.querySelector('[data-hex-transfer-whatsapp]');
      var transferContactBase = transferRoot.getAttribute('data-hex-transfer-contact-base') || '';
      var transferWhatsappBase = transferRoot.getAttribute('data-hex-transfer-whatsapp-base') || '';
      var transferPickerWraps = transferRoot.querySelectorAll('[data-hex-transfer-picker-wrap]');
      var transferStepItems = transferRoot.querySelectorAll('.hex-transfers-quote-card__stepper span');
      var readTransferMeta = function (fromValue, toValue) {
        var directKey = fromValue + '|' + toValue;
        var reverseKey = toValue + '|' + fromValue;

        if (transferRoutes[directKey]) {
          return transferRoutes[directKey];
        }
        if (transferRoutes[reverseKey]) {
          return transferRoutes[reverseKey];
        }
        return null;
      };

      try {
        transferRoutes = transferRoutesRaw ? JSON.parse(transferRoutesRaw) : {};
      } catch (e) {
        transferRoutes = {};
      }

      if (transferDate) {
        var now = new Date();
        var month = String(now.getMonth() + 1).padStart(2, '0');
        var day = String(now.getDate()).padStart(2, '0');
        var today = now.getFullYear() + '-' + month + '-' + day;
        transferDate.setAttribute('min', today);
      }

      var transferOpenPicker = function (field, allowClickFallback) {
        if (!field) {
          return;
        }

        try {
          field.focus({ preventScroll: true });
        } catch (e) {
          field.focus();
        }

        if (typeof field.showPicker === 'function') {
          try {
            field.showPicker();
            return;
          } catch (e) {
            // Fall back to click when showPicker is not available for the current control.
          }
        }

        if (allowClickFallback !== false && typeof field.click === 'function') {
          try {
            field.click();
          } catch (e) {
            // Ignore click fallback errors from browsers that block synthetic opening.
          }
        }
      };

      var transferIsOffPeak = function (value) {
        if (!value || value.indexOf(':') === -1) {
          return false;
        }

        var parts = value.split(':');
        var hours = parseInt(parts[0], 10);
        var minutes = parseInt(parts[1], 10);

        if (Number.isNaN(hours) || Number.isNaN(minutes)) {
          return false;
        }

        var totalMinutes = hours * 60 + minutes;
        return (totalMinutes >= 360 && totalMinutes <= 539) || (totalMinutes >= 1080 && totalMinutes <= 1259);
      };

      var transferBuildMessage = function () {
        var fromValue = transferFrom ? transferFrom.value : '';
        var toValue = transferTo ? transferTo.value : '';
        var passengersValue = transferPassengers ? transferPassengers.options[transferPassengers.selectedIndex].text : '';
        var luggageValue = transferLuggage ? transferLuggage.value : '';
        var dateValue = transferDate ? transferDate.value : '';
        var timeValue = transferTime ? transferTime.value : '';
        var notesValue = transferNotes ? transferNotes.value.trim() : '';
        var lines = ['Transfer inquiry'];

        if (fromValue && toValue) {
          lines.push('Route: ' + fromValue + ' -> ' + toValue);
        }
        if (dateValue) {
          lines.push('Date: ' + dateValue);
        }
        if (timeValue) {
          lines.push('Time: ' + timeValue);
        }
        if (passengersValue) {
          lines.push('Passengers: ' + passengersValue);
        }
        if (luggageValue) {
          lines.push('Luggage: ' + luggageValue);
        }
        if (notesValue) {
          lines.push('Notes: ' + notesValue);
        }

        return lines.join('\n');
      };

      var transferUpdateLinks = function () {
        if (!transferContact && !transferWhatsapp) {
          return;
        }

        var fromValue = transferFrom ? transferFrom.value : '';
        var toValue = transferTo ? transferTo.value : '';
        var dateValue = transferDate ? transferDate.value : '';
        var timeValue = transferTime ? transferTime.value : '';
        var passengersValue = transferPassengers ? transferPassengers.options[transferPassengers.selectedIndex].text : '';
        var luggageValue = transferLuggage ? transferLuggage.value : '';
        var notesValue = transferNotes ? transferNotes.value.trim() : '';
        var hasRoute = fromValue && toValue && fromValue !== toValue;

        if (transferContact) {
          try {
            var contactUrl = new URL(transferContactBase, window.location.origin);
            contactUrl.searchParams.set('subject', 'Transfer Inquiry');
            if (fromValue) {
              contactUrl.searchParams.set('from', fromValue);
            }
            if (toValue) {
              contactUrl.searchParams.set('to', toValue);
            }
            if (dateValue) {
              contactUrl.searchParams.set('date', dateValue);
            }
            if (timeValue) {
              contactUrl.searchParams.set('time', timeValue);
            }
            if (passengersValue) {
              contactUrl.searchParams.set('passengers', passengersValue);
            }
            if (luggageValue) {
              contactUrl.searchParams.set('luggage', luggageValue);
            }
            if (notesValue) {
              contactUrl.searchParams.set('notes', notesValue);
            }
            transferContact.setAttribute('href', contactUrl.toString());
          } catch (e) {
            transferContact.setAttribute('href', transferContactBase);
          }
          transferContact.classList.toggle('is-disabled', !hasRoute);
          transferContact.setAttribute('aria-disabled', hasRoute ? 'false' : 'true');
        }

        if (transferWhatsapp) {
          transferWhatsapp.setAttribute('href', transferWhatsappBase + '?text=' + encodeURIComponent(transferBuildMessage()));
          transferWhatsapp.classList.toggle('is-disabled', !hasRoute);
          transferWhatsapp.setAttribute('aria-disabled', hasRoute ? 'false' : 'true');
        }
      };

      var transferUpdateSummary = function () {
        var fromValue = transferFrom ? transferFrom.value : '';
        var toValue = transferTo ? transferTo.value : '';
        var dateValue = transferDate ? transferDate.value : '';
        var timeValue = transferTime ? transferTime.value : '';
        var passengerText = transferPassengers && transferPassengers.selectedIndex >= 0 ? transferPassengers.options[transferPassengers.selectedIndex].text : '';
        var hasPassengerSelection = passengerText && passengerText !== 'Choose size';
        var hasRoute = fromValue && toValue && fromValue !== toValue;
        var hasScheduleDetails = !!dateValue && !!timeValue && !!hasPassengerSelection;
        var meta = hasRoute ? readTransferMeta(fromValue, toValue) : null;
        var offPeak = transferIsOffPeak(timeValue);

        if (transferSummaryRoute) {
          transferSummaryRoute.textContent = hasRoute ? fromValue + ' to ' + toValue : 'Choose an origin and destination';
        }
        if (transferSummaryDuration) {
          transferSummaryDuration.textContent = meta ? meta.duration : 'Confirm duration with route selection';
        }
        if (transferSummaryGroup) {
          transferSummaryGroup.textContent = hasPassengerSelection ? passengerText : 'Passenger count not selected';
        }
        if (transferSummaryTiming) {
          if (dateValue || timeValue) {
            transferSummaryTiming.textContent = [dateValue || 'Date not set', timeValue || 'Time not set'].join(' / ');
          } else {
            transferSummaryTiming.textContent = 'Pick-up timing appears here';
          }
        }
        if (transferSummaryStatus) {
          if (hasRoute && meta) {
            transferSummaryStatus.textContent = meta.tag;
          } else if (hasRoute) {
            transferSummaryStatus.textContent = 'Custom private transfer';
          } else {
            transferSummaryStatus.textContent = 'Private speedboat transfer';
          }
        }
        if (transferSummaryNote) {
          if (!hasRoute) {
            transferSummaryNote.textContent = 'Confirm price and availability via Contact or WhatsApp, just like on your old transfer page, but in a cleaner layout.';
          } else if (offPeak) {
            transferSummaryNote.textContent = 'This timing falls into the better-value off-peak window. Final pricing and availability are still confirmed directly with you.';
          } else {
            transferSummaryNote.textContent = 'This request is ready to send. Final pricing and availability are confirmed directly with you based on route, weather, luggage, and group size.';
          }
        }
        if (transferSummaryOffpeak) {
          transferSummaryOffpeak.textContent = offPeak
            ? 'Off-peak window selected: this request may qualify for a better-value transfer slot.'
            : 'Standard timing selected. Try 06:00-08:59 or 18:00-20:59 for the better-value window.';
        }

        if (transferStepItems.length >= 3) {
          transferStepItems.forEach(function (item) {
            item.classList.remove('is-active');
            item.classList.remove('is-complete');
          });

          if (!hasRoute) {
            transferStepItems[0].classList.add('is-active');
          } else if (!hasScheduleDetails) {
            transferStepItems[0].classList.add('is-complete');
            transferStepItems[1].classList.add('is-active');
          } else {
            transferStepItems[0].classList.add('is-complete');
            transferStepItems[1].classList.add('is-complete');
            transferStepItems[2].classList.add('is-active');
          }
        }

        transferUpdateLinks();
      };

      if (transferSwap && transferFrom && transferTo) {
        transferSwap.addEventListener('click', function () {
          var oldFrom = transferFrom.value;
          transferFrom.value = transferTo.value;
          transferTo.value = oldFrom;
          transferUpdateSummary();
        });
      }

      transferPickerWraps.forEach(function (wrap) {
        var field = wrap.querySelector('select, input[type="date"], input[type="time"]');

        if (!field) {
          return;
        }

        wrap.addEventListener('click', function (event) {
          if (event.target === field) {
            return;
          }

          transferOpenPicker(field, true);
        });

        if (field.tagName === 'INPUT' && (field.type === 'date' || field.type === 'time')) {
          field.addEventListener('click', function () {
            transferOpenPicker(field, false);
          });
        }
      });

      [transferFrom, transferTo, transferDate, transferTime, transferPassengers, transferLuggage, transferNotes].forEach(function (field) {
        if (!field) {
          return;
        }

        field.addEventListener('change', transferUpdateSummary);
        if (field.tagName === 'TEXTAREA' || field.tagName === 'INPUT') {
          field.addEventListener('input', transferUpdateSummary);
        }
      });

      transferChips.forEach(function (chip) {
        chip.addEventListener('click', function () {
          var fromValue = chip.getAttribute('data-from') || '';
          var toValue = chip.getAttribute('data-to') || '';
          if (transferFrom) {
            transferFrom.value = fromValue;
          }
          if (transferTo) {
            transferTo.value = toValue;
          }
          transferUpdateSummary();

          if (transferFrom && typeof transferFrom.focus === 'function') {
            transferFrom.focus();
          }
        });
      });

      if (transferContact) {
        transferContact.addEventListener('click', function (event) {
          if (transferContact.getAttribute('aria-disabled') === 'true') {
            event.preventDefault();
          }
        });
      }

      if (transferWhatsapp) {
        transferWhatsapp.addEventListener('click', function (event) {
          if (transferWhatsapp.getAttribute('aria-disabled') === 'true') {
            event.preventDefault();
          }
        });
      }

      transferUpdateSummary();
    }

    var contactRoot = document.querySelector('[data-hex-contact-root]');
    if (contactRoot) {
      var contactTopicButtons = Array.prototype.slice.call(contactRoot.querySelectorAll('[data-hex-contact-topic]'));
      var contactTopicSelect = contactRoot.querySelector('[data-hex-contact-topic-select]');

      var contactSyncTopics = function (value) {
        contactTopicButtons.forEach(function (button) {
          var isActive = button.getAttribute('data-hex-contact-topic') === value;
          button.classList.toggle('is-active', isActive);
          button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
      };

      if (contactTopicSelect) {
        contactSyncTopics(contactTopicSelect.value);

        contactTopicButtons.forEach(function (button) {
          button.addEventListener('click', function () {
            var value = button.getAttribute('data-hex-contact-topic') || '';
            contactTopicSelect.value = value;
            contactSyncTopics(value);
            contactTopicSelect.dispatchEvent(new Event('change', { bubbles: true }));
          });
        });

        contactTopicSelect.addEventListener('change', function () {
          contactSyncTopics(contactTopicSelect.value);
        });
      }

      try {
        var contactUrl = new URL(window.location.href);
        if (contactUrl.searchParams.has('contact_status')) {
          contactUrl.searchParams.delete('contact_status');
          window.history.replaceState({}, document.title, contactUrl.pathname + (contactUrl.search ? contactUrl.search : '') + contactUrl.hash);
        }
      } catch (error) {
        // Keep the success message visible even if URL rewriting is unavailable.
      }
    }
  });
})();
