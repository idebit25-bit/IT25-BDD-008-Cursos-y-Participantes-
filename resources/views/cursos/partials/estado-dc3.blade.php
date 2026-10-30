{{-- Estado DC3 calculado (Cursos::estadoDc3). Sólo la excepción "no otorga DC3" es manual. --}}
@php
    $estadoDc3 = $estadoDc3 ?? [
        'estado' => \App\Models\Cursos::DC3_PENDIENTE,
        'requisitos' => [],
        'faltantes' => [],
        'noOtorga' => false,
        'heredadoDe' => null,
    ];
    $noOtorga = (bool) old('DC3NoOtorga', $estadoDc3['noOtorga']);
    $estadoCalculado = empty($estadoDc3['faltantes']) && !empty($estadoDc3['requisitos'])
        ? \App\Models\Cursos::DC3_SE_ENTREGA
        : \App\Models\Cursos::DC3_PENDIENTE;
    $estilos = [
        \App\Models\Cursos::DC3_SE_ENTREGA => ['bg-success', 'estado-verde', 'fa-check-circle'],
        \App\Models\Cursos::DC3_PENDIENTE => ['bg-warning text-dark', 'estado-amarillo', 'fa-hourglass-half'],
        \App\Models\Cursos::DC3_NO_SE_ENTREGA => ['bg-secondary', 'estado-verde', 'fa-ban'],
    ];
    $estadoVisible = $noOtorga ? \App\Models\Cursos::DC3_NO_SE_ENTREGA : $estadoCalculado;
@endphp

<div id="dc3Estado"
     data-calculado="{{ $estadoCalculado }}"
     data-no-otorga="{{ \App\Models\Cursos::DC3_NO_SE_ENTREGA }}"
     data-estilos='@json($estilos)'>
    <label class="form-label">
        Estado del DC3 <small class="text-muted">(automático)</small>
        <span class="estado-indicador {{ $estilos[$estadoVisible][1] }}" id="estado-DC3"></span>
    </label>

    <div class="mb-3">
        <span class="badge fs-6 px-3 py-2 {{ $estilos[$estadoVisible][0] }}" id="dc3Badge">
            <i class="fas {{ $estilos[$estadoVisible][2] }} me-1"></i>
            <span id="dc3Texto">{{ $estadoVisible }}</span>
        </span>
    </div>

    <div id="dc3Requisitos" @if($noOtorga) style="display: none;" @endif>
        <p class="small text-muted mb-2">
            Se calcula con la documentación STPS del paso 7
            @if($estadoDc3['heredadoDe'])
                del curso inicial <strong>{{ $estadoDc3['heredadoDe']->nomenclatura }}</strong>
            @endif
            :
        </p>
        <ul class="list-unstyled small mb-3">
            @foreach($estadoDc3['requisitos'] as $requisito => $cumplido)
                <li class="{{ $cumplido ? 'text-success' : 'text-danger' }}">
                    <i class="fas {{ $cumplido ? 'fa-check' : 'fa-times' }} me-2"></i>{{ $requisito }}
                </li>
            @endforeach
        </ul>
        @if(!empty($estadoDc3['faltantes']))
            <p class="small text-muted mb-3">Al completar lo que falta en el paso 7, el estado cambia solo.</p>
        @endif
    </div>

    <div class="form-check">
        <input type="hidden" name="DC3NoOtorga" value="0">
        <input class="form-check-input" type="checkbox" name="DC3NoOtorga" id="DC3NoOtorga" value="1" @checked($noOtorga)>
        <label class="form-check-label" for="DC3NoOtorga">Este curso no otorga DC3</label>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const contenedor = document.getElementById('dc3Estado');
    const casilla = document.getElementById('DC3NoOtorga');
    if (!contenedor || !casilla) return;

    const estilos = JSON.parse(contenedor.dataset.estilos);
    const badge = document.getElementById('dc3Badge');
    const indicador = document.getElementById('estado-DC3');

    casilla.addEventListener('change', function () {
        const estado = casilla.checked ? contenedor.dataset.noOtorga : contenedor.dataset.calculado;
        const [claseBadge, claseIndicador, icono] = estilos[estado];
        badge.className = 'badge fs-6 px-3 py-2 ' + claseBadge;
        badge.querySelector('i').className = 'fas ' + icono + ' me-1';
        document.getElementById('dc3Texto').textContent = estado;
        indicador.className = 'estado-indicador ' + claseIndicador;
        document.getElementById('dc3Requisitos').style.display = casilla.checked ? 'none' : '';
    });
});
</script>
