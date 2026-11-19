/* Menú lateral público: misma distribución que Profesores, siempre sobre el viewport. */
(function () {
  'use strict';
  var trigger = document.getElementById('tescoMenuToggle');
  var overlay = document.getElementById('tescoMenuOverlay');
  var panel = document.getElementById('menuLateral');
  if (!trigger || !overlay || !panel) return;

  // Trasladar al body evita que un contenedor con transform/filter limite position:fixed.
  if (overlay.parentNode !== document.body) document.body.appendChild(overlay);

  var previousFocus = null;
  var previousOverflow = '';
  var previousPaddingRight = '';

  function fitViewport() {
    // window.innerHeight corresponde al viewport efectivo, incluido el desplazamiento
    // y las barras de interfaz que alteran 100dvh en algunos navegadores.
    overlay.style.setProperty('position', 'fixed', 'important');
    overlay.style.setProperty('top', '0', 'important');
    overlay.style.setProperty('left', '0', 'important');
    overlay.style.setProperty('right', '0', 'important');
    overlay.style.setProperty('bottom', '0', 'important');
    overlay.style.setProperty('width', '100%', 'important');
    overlay.style.setProperty('height', window.innerHeight + 'px', 'important');
    overlay.style.setProperty('z-index', '9999', 'important');
  }
  function focusables() {
    return Array.prototype.slice.call(panel.querySelectorAll(
      'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
    )).filter(function (element) {
      return element.getClientRects().length > 0;
    });
  }
  function openMenu() {
    previousFocus = document.activeElement;
    previousOverflow = document.body.style.overflow;
    previousPaddingRight = document.body.style.paddingRight;
    fitViewport();
    overlay.hidden = false;
    // Conservar ancho de contenido si desaparece la scrollbar del documento.
    var scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    if (scrollbarWidth > 0) document.body.style.paddingRight = scrollbarWidth + 'px';
    document.body.style.overflow = 'hidden';
    document.body.classList.add('tesco-menu-open');
    trigger.setAttribute('aria-expanded', 'true');
    panel.setAttribute('aria-hidden', 'false');
    var closeButton = panel.querySelector('.tesco-menu-close');
    (closeButton || panel).focus();
  }
  function closeMenu() {
    overlay.hidden = true;
    document.body.classList.remove('tesco-menu-open');
    document.body.style.overflow = previousOverflow;
    document.body.style.paddingRight = previousPaddingRight;
    trigger.setAttribute('aria-expanded', 'false');
    panel.setAttribute('aria-hidden', 'true');
    if (previousFocus && typeof previousFocus.focus === 'function') previousFocus.focus();
  }
  trigger.addEventListener('click', function () {
    if (overlay.hidden) openMenu();
    else closeMenu();
  });
  overlay.addEventListener('click', function (event) {
    if (event.target.closest('[data-menu-close]')) closeMenu();
  });
  document.addEventListener('keydown', function (event) {
    if (overlay.hidden) return;
    if (event.key === 'Escape') {
      event.preventDefault();
      closeMenu();
    } else if (event.key === 'Tab') {
      var items = focusables();
      if (!items.length) {
        event.preventDefault();
        panel.focus();
        return;
      }
      var first = items[0];
      var last = items[items.length - 1];
      if (event.shiftKey && (document.activeElement === first || document.activeElement === panel)) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }
  });
  window.addEventListener('resize', function () {
    if (!overlay.hidden) fitViewport();
  });
}());
