{{-- Cancelar borra el borrador, por eso va por POST. El botón vive dentro del formulario del paso y se liga con form="formCancelarCreacion". --}}
<form id="formCancelarCreacion" action="{{ route('curso.cancelar') }}" method="POST" class="d-none">
    @csrf
</form>
