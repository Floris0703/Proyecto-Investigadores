/**
 * TESCo Investigadores | Treemap sin dependencias adicionales.
 * Cada celda se dimensiona según la fracción exacta de registros del grupo.
 * El algoritmo divide recursivamente cada región equilibrando las cantidades.
 */
(function () {
  'use strict';

  var root = document.getElementById('tescoTreemap');
  var dataElement = document.getElementById('tescoTreemapData');
  if (!root || !dataElement) return;

  var data;
  try {
    data = JSON.parse(dataElement.textContent).filter(function (item) {
      return item && typeof item.nombre === 'string' &&
        Number.isFinite(Number(item.cantidad)) && Number(item.cantidad) > 0;
    });
  } catch (error) {
    root.textContent = 'No fue posible dibujar la distribución académica.';
    return;
  }
  if (!data.length) return;

  var total = data.reduce(function (sum, item) {
    return sum + Number(item.cantidad);
  }, 0);
  var number = new Intl.NumberFormat('es-MX');
  var percentage = new Intl.NumberFormat('es-MX', {
    minimumFractionDigits: 1, maximumFractionDigits: 1
  });
  // Escala secuencial de un solo verde: menor participación = claro,
  // mayor participación = oscuro. El color se calcula con el porcentaje real.
  var shares = data.map(function (item) {
    return 100 * Number(item.cantidad) / total;
  });
  var minShare = Math.min.apply(null, shares);
  var maxShare = Math.max.apply(null, shares);

  function greenForShare(share) {
    var t = maxShare === minShare ? 0.5 :
      (share - minShare) / (maxShare - minShare);
    var light = [215, 237, 143]; // verde TESCo claro
    var dark = [39, 93, 21];     // verde TESCo oscuro
    var rgb = light.map(function (channel, i) {
      return Math.round(channel + (dark[i] - channel) * t);
    });
    return { color: 'rgb(' + rgb.join(', ') + ')', darkText: t < 0.5 };
  }

  function makeTile(item, x, y, width, height) {
    var cell = document.createElement('div');
    var amount = Number(item.cantidad);
    var share = 100 * amount / total;
    cell.className = 'tesco-treemap-celda';
    cell.style.left = x + '%';
    cell.style.top = y + '%';
    cell.style.width = width + '%';
    cell.style.height = height + '%';
    var green = greenForShare(share);
    cell.style.backgroundColor = green.color;
    cell.style.color = green.darkText ? '#183612' : '#ffffff';
    cell.style.textShadow = green.darkText ? 'none' : '0 1px 3px rgba(0, 0, 0, .45)';
    cell.title = item.nombre + ': ' + number.format(amount) + ' (' + percentage.format(share) + '%)';

    var inner = document.createElement('div');
    inner.className = 'tesco-treemap-interior';

    var label = document.createElement('strong');
    label.className = 'tesco-treemap-nombre';
    label.textContent = item.nombre;
    inner.appendChild(label);

    var detail = document.createElement('span');
    detail.className = 'tesco-treemap-detalle';
    detail.textContent = number.format(amount) + ' · ' + percentage.format(share) + '%';
    inner.appendChild(detail);

    cell.appendChild(inner);
    root.appendChild(cell);

    // En bloques pequeños el detalle sigue disponible al pasar el cursor.
    if (cell.clientWidth < 115 || cell.clientHeight < 65) {
      cell.classList.add('tesco-treemap-compacta');
    }
  }

  function layout(items, x, y, width, height) {
    if (!items.length) return;
    if (items.length === 1) {
      makeTile(items[0].value, x, y, width, height);
      return;
    }

    var sum = items.reduce(function (n, item) { return n + Number(item.value.cantidad); }, 0);
    var half = sum / 2;
    var partial = 0;
    var split = 1;
    var best = Infinity;
    for (var i = 1; i < items.length; i++) {
      partial += Number(items[i - 1].value.cantidad);
      var distance = Math.abs(partial - half);
      if (distance < best) {
        best = distance;
        split = i;
      }
    }

    var left = items.slice(0, split);
    var right = items.slice(split);
    var leftSum = left.reduce(function (n, item) { return n + Number(item.value.cantidad); }, 0);
    var ratio = leftSum / sum;
    if (width >= height) {
      layout(left, x, y, width * ratio, height);
      layout(right, x + width * ratio, y, width * (1 - ratio), height);
    } else {
      layout(left, x, y, width, height * ratio);
      layout(right, x, y + height * ratio, width, height * (1 - ratio));
    }
  }

  var indexed = data.map(function (item) { return { value: item }; });
  layout(indexed, 0, 0, 100, 100);
  root.setAttribute('aria-label', 'Distribución proporcional de producción académica: ' +
    data.map(function (item) {
      return item.nombre + ': ' + number.format(Number(item.cantidad)) +
        ' (' + percentage.format(100 * Number(item.cantidad) / total) + '%)';
    }).join('; ') + '.');

  // Recalcula la legibilidad de bloques en cambios de tamaño de la ventana.
  function compact() {
    var cells = root.querySelectorAll('.tesco-treemap-celda');
    Array.prototype.forEach.call(cells, function (cell) {
      cell.classList.toggle('tesco-treemap-compacta', cell.clientWidth < 115 || cell.clientHeight < 65);
    });
  }
  if ('ResizeObserver' in window) {
    new ResizeObserver(compact).observe(root);
  } else {
    window.addEventListener('resize', compact);
  }
  compact();
}());
