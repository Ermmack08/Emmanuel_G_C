const canvas = document.getElementById('signature-pad');
const ctx = canvas.getContext('2d');
const clearButton = document.getElementById('clear-button');
const saveButton = document.getElementById('save-button');
const msg = document.getElementById('msg');

// Ajuste canvas
function resizeCanvas() {
  canvas.width = canvas.parentElement.clientWidth;
  canvas.height = 150;
  ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#000';
}
resizeCanvas(); window.addEventListener('resize', resizeCanvas);

let isDrawing = false, hasDrawn = false;
canvas.addEventListener('mousedown', e => { isDrawing = true; hasDrawn = true; ctx.beginPath(); ctx.moveTo(e.offsetX, e.offsetY); });
canvas.addEventListener('mousemove', e => { if (isDrawing) { ctx.lineTo(e.offsetX, e.offsetY); ctx.stroke(); }});
canvas.addEventListener('mouseup',   () => isDrawing = false);
canvas.addEventListener('mouseleave',() => isDrawing = false);
canvas.addEventListener('touchstart', e => { e.preventDefault(); const rect=canvas.getBoundingClientRect(); const t=e.touches[0]; isDrawing=true; hasDrawn=true; ctx.beginPath(); ctx.moveTo(t.clientX-rect.left, t.clientY-rect.top); });
canvas.addEventListener('touchmove', e => { e.preventDefault(); if(!isDrawing) return; const rect=canvas.getBoundingClientRect(); const t=e.touches[0]; ctx.lineTo(t.clientX-rect.left, t.clientY-rect.top); ctx.stroke(); });
canvas.addEventListener('touchend', e => { e.preventDefault(); isDrawing=false; });

clearButton.addEventListener('click', () => { ctx.clearRect(0,0,canvas.width,canvas.height); hasDrawn = false; });

function showMsg(text, ok=true){
  msg.style.display='block';
  msg.style.color = ok ? '#2e7d32' : '#c62828';
  msg.textContent = text;
  setTimeout(()=>{ msg.style.display='none'; }, 4500);
}

saveButton.addEventListener('click', async () => {
  document.querySelectorAll('.error-message').forEach(e => e.style.display = 'none');
  document.querySelectorAll('.error').forEach(e => e.classList.remove('error'));

  const tipoDocumento = document.getElementById('tipoDocumento');
  const documento = document.getElementById('documento');
  const archivoFirma = document.getElementById('archivoFirma');

  let valid = true;
  if (!tipoDocumento.value) { tipoDocumento.classList.add('error'); document.getElementById('tipoDocumentoError').style.display='block'; valid=false; }
  if (!documento.value)     { documento.classList.add('error');     document.getElementById('documentoError').style.display='block';     valid=false; }

  if (!valid) { showMsg('Por favor, complete todos los campos obligatorios.', false); return; }

  if (!archivoFirma.files.length && !hasDrawn){
    showMsg('Debe adjuntar una firma o dibujarla en el cuadro.', false);
    return;
  }

  const fd = new FormData();
  fd.append('tipoDocumento', tipoDocumento.value);
  fd.append('documento', documento.value);

  if (archivoFirma.files.length) {
    fd.append('archivoFirma', archivoFirma.files[0]); // prioriza archivo
  } else if (hasDrawn) {
    // Convertir canvas a Blob PNG
    const blob = await new Promise(res => canvas.toBlob(res, 'image/png'));
    fd.append('firmaCanvas', blob, `firma_${documento.value}.png`);
  }

  try {
    const resp = await fetch('renovar_matricula.php', {
      method: 'POST',
      body: fd
    });

    // Mostrar info de respuesta en consola
    console.log('HTTP status:', resp.status, resp.statusText);

    // Intentar leer texto bruto (por si el servidor devuelve HTML o errores)
    const text = await resp.text();
    console.log('Raw response body:', text);

    // Intentar parsear JSON (protege con try/catch)
    let data = null;
    try {
      data = JSON.parse(text);
      console.log('Parsed JSON:', data);
    } catch(parseErr) {
      console.warn('No JSON received (parse failed):', parseErr);
    }

    if (resp.ok && data && data.ok) {
      showMsg('Renovación registrada y firma guardada correctamente.');
    } else {
      // Mostrar mensaje concreto si existe
      const errMsg = data?.error || text || ('HTTP ' + resp.status);
      showMsg('Error: ' + errMsg, false);
    }
  } catch (e) {
    // Error de red (no conexión, CORS, archivo no encontrado, etc.)
    console.error('Fetch failed:', e);
    showMsg('Error de red/servidor al enviar la renovación. Ver consola para más detalles.', false);
  }

});
