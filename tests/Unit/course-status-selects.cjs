// Ejecutar: node tests/Unit/course-status-selects.cjs
const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const path = require('node:path');

for (const name of ['paso6', 'edit-paso6', 'paso7', 'edit-paso7']) {
    const source = fs.readFileSync(path.join(__dirname, '../../resources/views/cursos', name + '.blade.php'), 'utf8');
    const validator = source.match(/function validarSelect\(select\) \{[\s\S]*?\n\}/)[0];
    const classes = () => {
        const values = new Set();
        return { add: (...v) => v.forEach(x => values.add(x)), remove: (...v) => v.forEach(x => values.delete(x)), contains: x => values.has(x) };
    };
    const indicator = { classList: classes() };
    const field = { id: 'estado', value: '', classList: classes() };
    const context = { document: { getElementById: () => indicator }, actualizarContadores() {}, actualizarEstadoGeneral() {} };
    vm.createContext(context);
    vm.runInContext(validator, context);
    // Incluye transiciones para detectar colores anteriores que no se limpien.
    for (const [value, expected] of [
        ['Entrega pendiente de DC3', 'estado-amarillo'],
        ['Se entrega DC3', 'estado-verde'],
        ['En proceso', 'estado-amarillo'],
        ['', 'estado-rojo'],
        ['Ya obtenida', 'estado-verde'],
    ]) {
        field.value = value;
        context.validarSelect(field);
        for (const color of ['estado-verde', 'estado-amarillo', 'estado-rojo']) {
            assert.equal(indicator.classList.contains(color), color === expected, `${name}: ${value} / ${color}`);
        }
    }
    console.log(name + ': OK');
}
