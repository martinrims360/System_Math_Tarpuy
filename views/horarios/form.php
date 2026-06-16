<?php // views/horarios/form.php
$esEdicion = !is_null($horario);
$accion    = $esEdicion ? 'update' : 'store';

$val = [
    'fecha'         => $horario['fecha']        ?? $_POST['fecha']        ?? date('Y-m-d'),
    'id_docente'    => $horario['id_docente']   ?? $_POST['id_docente']   ?? '',
    'id_grupo'      => $horario['id_grupo']     ?? $_POST['id_grupo']     ?? '',
    'id_salon'      => $horario['id_salon']     ?? $_POST['id_salon']     ?? '',
    'dia_semana'    => $horario['dia_semana']   ?? $_POST['dia_semana']   ?? '',
    'hora_inicio'   => $horario['hora_inicio']  ?? $_POST['hora_inicio']  ?? '08:00',
    'hora_fin'      => $horario['hora_fin']     ?? $_POST['hora_fin']     ?? '10:00',
    'observaciones' => $horario['observaciones']?? $_POST['observaciones']?? '',
];
// Recortar segundos si vienen como HH:MM:SS
$val['hora_inicio'] = substr($val['hora_inicio'], 0, 5);
$val['hora_fin']    = substr($val['hora_fin'],    0, 5);
?>

<div class="page-header">
  <div>
    <h1>
      <i class="ti ti-<?= $esEdicion ? 'edit' : 'calendar-plus' ?>"
         style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>
      <?= $esEdicion ? 'Editar horario' : 'Registrar horario' ?>
    </h1>
    <p><?= $esEdicion ? 'Modifica los datos del horario' : 'Programa una sesión para un docente, grupo y salón' ?></p>
  </div>
  <a href="index.php?page=horarios" class="btn">
    <i class="ti ti-arrow-left"></i> Volver
  </a>
</div>

<!-- Errores -->
<?php if (!empty($errors)): ?>
  <div style="background:#FAECE7;border-left:4px solid #C0392B;color:#712B13;
              padding:12px 16px;border-radius:10px;margin-bottom:20px">
    <div style="font-weight:500;margin-bottom:6px;display:flex;align-items:center;gap:6px">
      <i class="ti ti-alert-circle"></i> Corrige los siguientes errores:
    </div>
    <ul style="margin:0;padding-left:18px">
      <?php foreach ($errors as $e): ?>
        <li style="font-size:13px"><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card">
  <form method="POST" action="index.php?page=horarios&action=<?= $accion ?>">

    <?php if ($esEdicion): ?>
      <input type="hidden" name="id" value="<?= $horario['id_horario'] ?>">
    <?php endif; ?>

    <div class="form-grid">

      <!-- Docente -->
      <?php if (Auth::isCoord()): ?>
      <div class="form-group">
        <label>Docente <span style="color:#C0392B">*</span></label>
        <select name="id_docente" required>
          <option value="">Seleccionar...</option>
          <?php foreach ($docentes as $d): ?>
            <option value="<?= $d['id_docente'] ?>"
              <?= $val['id_docente'] == $d['id_docente'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($d['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php else: ?>
      <div class="form-group">
        <label>Docente</label>
        <input type="text" value="<?= htmlspecialchars(Auth::user()['nombre']) ?>"
               disabled style="background:#F8F9FA;color:#6C757D">
      </div>
      <?php endif; ?>

      <!-- Grupo -->
      <div class="form-group">
        <label>Grupo <span style="color:#C0392B">*</span></label>
        <select name="id_grupo" required>
          <option value="">Seleccionar...</option>
          <?php foreach ($grupos as $g): ?>
            <option value="<?= $g['id_grupo'] ?>"
              <?= $val['id_grupo'] == $g['id_grupo'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($g['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Salón -->
      <div class="form-group">
        <label>Salón <span style="color:#C0392B">*</span></label>
        <select name="id_salon" required>
          <option value="">Seleccionar...</option>
          <?php foreach ($salones as $s): ?>
            <option value="<?= $s['id_salon'] ?>"
              <?= $val['id_salon'] == $s['id_salon'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['nombre']) ?>
              <?= $s['ubicacion'] ? ' — ' . $s['ubicacion'] : '' ?>
              <?= $s['capacidad'] ? ' (' . $s['capacidad'] . ' pers.)' : '' ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Día -->
      <div class="form-group">
        <label>Día de la semana <span style="color:#C0392B">*</span></label>
        <select name="dia_semana" required>
          <option value="">Seleccionar...</option>
          <?php foreach ($dias as $d): ?>
            <option value="<?= $d ?>"
              <?= $val['dia_semana'] === $d ? 'selected' : '' ?>>
              <?= $d ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Fecha -->
      <div class="form-group">
        <label>Fecha <span style="color:#C0392B">*</span></label>
        <input type="date" name="fecha" required
               value="<?= htmlspecialchars($val['fecha']) ?>">
      </div>

      <!-- Hora inicio -->
      <div class="form-group">
        <label>Hora de inicio <span style="color:#C0392B">*</span></label>
        <input type="time" name="hora_inicio" required
               value="<?= htmlspecialchars($val['hora_inicio']) ?>">
      </div>

      <!-- Hora fin -->
      <div class="form-group">
        <label>Hora de fin <span style="color:#C0392B">*</span></label>
        <input type="time" name="hora_fin" required
               value="<?= htmlspecialchars($val['hora_fin']) ?>">
        <span style="font-size:12px;color:#ADB5BD;margin-top:3px">
          Debe ser mayor a la hora de inicio
        </span>
      </div>

      <!-- Observaciones -->
      <div class="form-group form-full">
        <label>
          Observaciones
          <span style="font-weight:400;color:#ADB5BD">(opcional)</span>
        </label>
        <textarea name="observaciones"
                  placeholder="Notas sobre la sesión, materiales, indicaciones especiales..."
                  style="min-height:80px"><?= htmlspecialchars($val['observaciones']) ?></textarea>
      </div>

    </div><!-- /form-grid -->

    <!-- Info visual de duración -->
    <div id="duracion-info"
         style="background:#F0EFFD;border-radius:8px;padding:10px 14px;
                font-size:13px;color:#3C3489;margin-top:4px;margin-bottom:4px;display:none">
      <i class="ti ti-clock" style="vertical-align:-2px;margin-right:5px"></i>
      <span id="duracion-txt"></span>
    </div>

    <div class="form-actions">
      <a href="index.php?page=horarios" class="btn">Cancelar</a>
      <button type="submit" class="btn btn-primary">
        <i class="ti ti-<?= $esEdicion ? 'device-floppy' : 'calendar-plus' ?>"></i>
        <?= $esEdicion ? 'Guardar cambios' : 'Registrar horario' ?>
      </button>
    </div>

  </form>
</div>

<script>
// Calcular duración en tiempo real
function calcDuracion() {
    const hi = document.querySelector('[name="hora_inicio"]').value;
    const hf = document.querySelector('[name="hora_fin"]').value;
    const info = document.getElementById('duracion-info');
    const txt  = document.getElementById('duracion-txt');

    if (!hi || !hf) { info.style.display = 'none'; return; }

    const [h1, m1] = hi.split(':').map(Number);
    const [h2, m2] = hf.split(':').map(Number);
    const mins = (h2 * 60 + m2) - (h1 * 60 + m1);

    if (mins <= 0) {
        info.style.background = '#FAECE7';
        info.style.color = '#712B13';
        txt.textContent = 'La hora de fin debe ser mayor a la hora de inicio.';
        info.style.display = 'block';
        return;
    }

    const horas = Math.floor(mins / 60);
    const minutos = mins % 60;
    let durStr = '';
    if (horas > 0)   durStr += horas + ' hora' + (horas > 1 ? 's' : '');
    if (minutos > 0) durStr += (horas > 0 ? ' ' : '') + minutos + ' min';

    info.style.background = '#F0EFFD';
    info.style.color = '#3C3489';
    txt.textContent = 'Duración de la sesión: ' + durStr;
    info.style.display = 'block';
}

document.querySelector('[name="hora_inicio"]').addEventListener('change', calcDuracion);
document.querySelector('[name="hora_fin"]').addEventListener('change', calcDuracion);
calcDuracion(); // ejecutar al cargar (modo edición)
</script>