<?php // views/temas/form.php
$esEdicion = !is_null($tema);
$accion    = $esEdicion ? 'update' : 'store';

// Valores actuales (edición) o POST (rebote de error) o vacío
$val = [
    'fecha'         => $tema['fecha']         ?? $_POST['fecha']         ?? date('Y-m-d'),
    'id_docente'    => $tema['id_docente']    ?? $_POST['id_docente']    ?? '',
    'id_grupo'      => $tema['id_grupo']      ?? $_POST['id_grupo']      ?? '',
    'id_area'       => $tema['id_area']       ?? $_POST['id_area']       ?? '',
    'id_salon'      => $tema['id_salon']      ?? $_POST['id_salon']      ?? '',
    'tema'          => $tema['tema']          ?? $_POST['tema']          ?? '',
    'subtema'       => $tema['subtema']       ?? $_POST['subtema']       ?? '',
    'observaciones' => $tema['observaciones'] ?? $_POST['observaciones'] ?? '',
];
?>

<div class="page-header">
  <div>
    <h1>
      <i class="ti ti-<?= $esEdicion ? 'edit' : 'book-plus' ?>"
         style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>
      <?= $esEdicion ? 'Editar tema' : 'Registrar tema desarrollado' ?>
    </h1>
    <p><?= $esEdicion ? 'Modifica los datos del registro' : 'Completa el formulario con el contenido trabajado en la sesión' ?></p>
  </div>
  <a href="index.php?page=temas" class="btn">
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
  <form method="POST" action="index.php?page=temas&action=<?= $accion ?>">

    <?php if ($esEdicion): ?>
      <input type="hidden" name="id" value="<?= $tema['id_registro'] ?>">
    <?php endif; ?>

    <div class="form-grid">

      <!-- Fecha -->
      <div class="form-group">
        <label>Fecha de la sesión <span style="color:#C0392B">*</span></label>
        <input type="date" name="fecha" required value="<?= htmlspecialchars($val['fecha']) ?>">
      </div>

      <!-- Docente (solo coordinador puede elegir) -->
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
        <input type="text" value="<?= htmlspecialchars(Auth::user()['nombre']) ?>" disabled
               style="background:#F8F9FA;color:#6C757D">
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

      <!-- Área -->
      <div class="form-group">
        <label>Área temática <span style="color:#C0392B">*</span></label>
        <select name="id_area" required>
          <option value="">Seleccionar...</option>
          <?php foreach ($areas as $a): ?>
            <option value="<?= $a['id_area'] ?>"
              <?= $val['id_area'] == $a['id_area'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($a['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Salón (opcional) -->
      <div class="form-group">
        <label>
          Salón
          <span style="font-weight:400;color:#ADB5BD">(opcional)</span>
        </label>
        <select name="id_salon">
          <option value="">Sin especificar</option>
          <?php foreach ($salones as $s): ?>
            <option value="<?= $s['id_salon'] ?>"
              <?= $val['id_salon'] == $s['id_salon'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['nombre']) ?>
              <?= $s['ubicacion'] ? ' — ' . $s['ubicacion'] : '' ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Tema principal -->
      <div class="form-group form-full">
        <label>Tema principal <span style="color:#C0392B">*</span></label>
        <input type="text" name="tema" required
               placeholder="Ej: Divisibilidad, Triángulos semejantes, Principio de Casillas..."
               value="<?= htmlspecialchars($val['tema']) ?>">
      </div>

      <!-- Subtema -->
      <div class="form-group form-full">
        <label>
          Subtema
          <span style="font-weight:400;color:#ADB5BD">(opcional)</span>
        </label>
        <input type="text" name="subtema"
               placeholder="Ej: Criterios de divisibilidad, Casos de congruencia..."
               value="<?= htmlspecialchars($val['subtema']) ?>">
      </div>

      <!-- Observaciones -->
      <div class="form-group form-full">
        <label>
          Observaciones
          <span style="font-weight:400;color:#ADB5BD">(opcional)</span>
        </label>
        <textarea name="observaciones"
                  placeholder="Nivel de dificultad, materiales usados, pendientes para próxima sesión..."
                  style="min-height:90px"><?= htmlspecialchars($val['observaciones']) ?></textarea>
      </div>

    </div><!-- /form-grid -->

    <div class="form-actions">
      <a href="index.php?page=temas" class="btn">Cancelar</a>
      <button type="submit" class="btn btn-primary">
        <i class="ti ti-<?= $esEdicion ? 'device-floppy' : 'book-plus' ?>"></i>
        <?= $esEdicion ? 'Guardar cambios' : 'Registrar tema' ?>
      </button>
    </div>

  </form>
</div>