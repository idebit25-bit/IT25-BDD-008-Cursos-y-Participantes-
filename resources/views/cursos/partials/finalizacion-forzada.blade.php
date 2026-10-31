{{-- "Forzar guardado": antes de finalizar se guarda lo capturado en el paso actual,
     para no perderlo. Si el paso tiene errores de validación se envía el formulario
     normalmente y se muestran los errores en lugar de finalizar. --}}
<script>
function guardarPasoActual() {
    const form = document.getElementById('formularioCurso');
    if (!form) return Promise.resolve(false);

    return fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'Accept': 'text/html' },
        redirect: 'follow'
    }).then(function (respuesta) {
        const destino = new URL(respuesta.url).pathname;
        // Laravel regresa a la misma página cuando la validación falla
        if (destino === window.location.pathname) {
            form.submit();
            return new Promise(function () {});
        }
        // El paso 7 ya finaliza el curso y regresa al listado
        return destino === new URL('{{ route('cursos.index') }}').pathname;
    });
}

// Reemplazo directo de fetch() para la llamada de finalización forzada
function finalizarConPasoActual(url, opciones) {
    return guardarPasoActual().then(function (terminado) {
        return terminado
            ? new Response(JSON.stringify({ success: true }), { headers: { 'Content-Type': 'application/json' } })
            : fetch(url, opciones);
    });
}
</script>
