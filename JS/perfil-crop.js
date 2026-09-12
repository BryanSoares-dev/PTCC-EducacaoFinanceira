(() => {
  'use strict';

  const input = document.querySelector('#foto');
  const modal = document.querySelector('#cropModal');
  const canvas = document.querySelector('#cropCanvas');
  const preview = document.querySelector('#cropPreview');
  const zoom = document.querySelector('#cropZoom');
  const confirmButton = document.querySelector('#cropConfirm');
  const cancelButtons = document.querySelectorAll('[data-crop-close]');
  const form = document.querySelector('.perfil-avatar-form');
  if (!input || !modal || !canvas || !preview || !zoom || !confirmButton || !form) return;

  const ctx = canvas.getContext('2d', { alpha: false });
  const state = { image: null, scale: 1, minScale: 1, offsetX: 0, offsetY: 0, dragging: false, startX: 0, startY: 0 };
  const SIZE = 720;
  canvas.width = SIZE;
  canvas.height = SIZE;

  const clamp = (value, min, max) => Math.min(max, Math.max(min, value));
  const fitScale = (image) => Math.max(SIZE / image.naturalWidth, SIZE / image.naturalHeight);

  function draw() {
    if (!state.image) return;
    const image = state.image;
    const width = image.naturalWidth * state.scale;
    const height = image.naturalHeight * state.scale;
    const limitX = Math.max(0, (width - SIZE) / 2);
    const limitY = Math.max(0, (height - SIZE) / 2);
    state.offsetX = clamp(state.offsetX, -limitX, limitX);
    state.offsetY = clamp(state.offsetY, -limitY, limitY);
    ctx.clearRect(0, 0, SIZE, SIZE);
    ctx.fillStyle = '#06182d';
    ctx.fillRect(0, 0, SIZE, SIZE);
    ctx.drawImage(image, (SIZE - width) / 2 + state.offsetX, (SIZE - height) / 2 + state.offsetY, width, height);
    preview.src = canvas.toDataURL('image/jpeg', .88);
  }

  function openEditor(file) {
    if (!file || !file.type.startsWith('image/')) return;
    const url = URL.createObjectURL(file);
    const image = new Image();
    image.onload = () => {
      URL.revokeObjectURL(url);
      state.image = image;
      state.minScale = fitScale(image);
      state.scale = state.minScale;
      state.offsetX = 0;
      state.offsetY = 0;
      zoom.min = String(state.minScale);
      zoom.max = String(state.minScale * 3.5);
      zoom.step = String(Math.max(.01, state.minScale / 50));
      zoom.value = String(state.scale);
      modal.hidden = false;
      document.body.classList.add('crop-modal-open');
      draw();
    };
    image.onerror = () => URL.revokeObjectURL(url);
    image.src = url;
  }

  input.addEventListener('change', () => openEditor(input.files[0]));
  zoom.addEventListener('input', () => { state.scale = Number(zoom.value); draw(); });

  function pointerStart(event) {
    if (!state.image) return;
    state.dragging = true;
    const point = event.touches ? event.touches[0] : event;
    state.startX = point.clientX - state.offsetX;
    state.startY = point.clientY - state.offsetY;
    canvas.setPointerCapture?.(event.pointerId);
  }
  function pointerMove(event) {
    if (!state.dragging) return;
    const point = event.touches ? event.touches[0] : event;
    state.offsetX = point.clientX - state.startX;
    state.offsetY = point.clientY - state.startY;
    draw();
  }
  function pointerEnd() { state.dragging = false; }
  canvas.addEventListener('pointerdown', pointerStart);
  canvas.addEventListener('pointermove', pointerMove);
  canvas.addEventListener('pointerup', pointerEnd);
  canvas.addEventListener('pointercancel', pointerEnd);
  canvas.addEventListener('wheel', (event) => {
    event.preventDefault();
    const next = state.scale + (event.deltaY < 0 ? state.minScale / 20 : -state.minScale / 20);
    state.scale = clamp(next, Number(zoom.min), Number(zoom.max));
    zoom.value = String(state.scale);
    draw();
  }, { passive: false });

  function closeEditor() {
    modal.hidden = true;
    document.body.classList.remove('crop-modal-open');
    input.value = '';
    state.image = null;
  }
  cancelButtons.forEach((button) => button.addEventListener('click', closeEditor));
  modal.addEventListener('click', (event) => { if (event.target === modal) closeEditor(); });
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && !modal.hidden) closeEditor(); });

  confirmButton.addEventListener('click', () => {
    if (!state.image || confirmButton.disabled) return;
    confirmButton.disabled = true;
    canvas.toBlob((blob) => {
      if (!blob) { confirmButton.disabled = false; return; }
      const cropped = new File([blob], 'avatar-crop.jpg', { type: 'image/jpeg', lastModified: Date.now() });
      const transfer = new DataTransfer();
      transfer.items.add(cropped);
      input.files = transfer.files;
      form.submit();
    }, 'image/jpeg', .92);
  });
})();
