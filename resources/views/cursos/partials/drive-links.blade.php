<div class="course-drive-links">
    @php
        $documentos = collect([$curso->recursos, $curso->evaluaciones, $curso->certificaciones])->flatten();
        $enlaces = $documentos->filter(fn ($doc) => filter_var($doc->drive_url, FILTER_VALIDATE_URL)
            && in_array(strtolower(parse_url($doc->drive_url, PHP_URL_SCHEME) ?? ''), ['http', 'https'], true));
    @endphp
    @forelse($enlaces as $documento)
        <a href="{{ $documento->drive_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-external-link-alt me-1" aria-hidden="true"></i>
            {{ \Illuminate\Support\Str::headline($documento->tipo_recurso ?? $documento->tipo_evaluacion ?? $documento->tipo_certificacion) }}
        </a>
    @empty
        <span class="text-muted small">Este curso aún no tiene enlaces de Drive registrados.</span>
    @endforelse
</div>
