{{-- Aviso del paso 7 cuando el subcurso comparte la documentación STPS de su curso inicial --}}
@if($stpsHeredadoDe)
    <div class="alert alert-info d-flex align-items-start shadow-sm" role="status" id="stps-heredado">
        <i class="fas fa-link me-3 mt-1"></i>
        <div>
            <strong>Documentación STPS tomada del curso inicial.</strong>
            <div class="small mt-1">
                Este subcurso usa la fecha de registro, el formato DC5, el certificado de comprobación y la carta poder de
                <strong>{{ $stpsHeredadoDe->nomenclatura }} — {{ $stpsHeredadoDe->nombre }}</strong>.
                Para modificarlos, edita el paso 7 del curso inicial. UDEMY sí se captura aquí.
            </div>
            @if(auth()->user()?->puesto != 'Operacion')
                <a href="{{ route('cursos.edit.paso', [$stpsHeredadoDe->id, 7]) }}" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="fas fa-edit me-1"></i> Editar STPS del curso inicial
                </a>
            @endif
        </div>
    </div>
@elseif($puedeUsarStpsInicial)
    <div class="alert alert-warning d-flex align-items-start shadow-sm" role="status">
        <i class="fas fa-info-circle me-3 mt-1"></i>
        <div>
            <strong>Este subcurso tiene documentación STPS propia.</strong>
            <div class="small mt-1">
                El curso inicial ya tiene su alta STPS. Puedes conservar los datos de este subcurso o descartarlos para usar los del curso inicial.
            </div>
            <form action="{{ route('cursos.stps.usar-inicial', $curso->id) }}" method="POST" class="mt-2"
                  onsubmit="return confirm('Se descartarán la fecha de registro, DC5, certificado y carta poder de este subcurso para usar los del curso inicial. ¿Continuar?')">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-dark">
                    <i class="fas fa-link me-1"></i> Usar datos del curso inicial
                </button>
            </form>
        </div>
    </div>
@endif
