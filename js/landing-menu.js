/* Navegación pública TESCo: panel lateral, independiente de Bootstrap/MDB. */
(function () {
  'use strict';
  var trigger = document.getElementById('tescoMenuToggle');
  var overlay = document.getElementById('tescoMenuOverlay');
  var panel = document.getElementById('menuLateral');
  if (!trigger || !overlay || !panel) return;

  var previousFocus = null;
  function focusables() {
    return Array.prototype.slice.call(panel.querySelectorAll(
      'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
    )).filter(function (element) { return element.getClientRects().length > 0; });
  }
  function openMenu() {
    previousFocus = document.activeElement;
    overlay.hidden = false;
    document.body.classList.add('tesco-menu-open');
    trigger.setAttribute('aria-expanded', 'true');
    panel.setAttribute('aria-hidden', 'false');
    var first = panel.querySelector('[data-menu-close]');
    (first || panel).focus();
  }
  function closeMenu() {
    overlay.hidden = true;
    document.body.classList.remove('tesco-menu-open');
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
      if (!items.length) { event.preventDefault(); panel.focus(); return; }
      var first = items[0], last = items[items.length - 1];
      if (event.shiftKey && (document.activeElement === first || document.activeElement === panel)) {
        event.preventDefault(); last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault(); first.focus();
      }
    }
  });
}());
