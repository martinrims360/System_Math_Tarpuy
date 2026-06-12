<?php // views/salones/form.php
$esEdicion = !is_null($salon);
$accion    = $esEdicion ? 'update' : 'store';
?>

<div class="page-header">
  <div>
    <h1>
      <i class="ti ti-<?= $esEdicion ? 'edit' : 'door' ?>"
         style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>
      <?= $esEdicion ? 'Editar salón' : 'Nuevo salón' ?>
    </h1>
  </div>
  <a href="index.php?page=salones" class="btn"><i class="ti ti-arrow-left"></i> Volver</a>
</div>

<?php if (!empty($errors)): ?>
  <div style="background:#FAECE7;border-left:4px solid #C0392B;color:#712B13;
              padding:12px 16px;border-radius:10px;margin-bottom:20px">
    <div style="font-weight:500;margin-bottom:6px"><i class="ti ti-alert-circle"></i> Errores:</div>
    <ul style="margin:0;padding-left:18px">
      <?php foreach ($errors as $e): ?>
        <li style="font-size:13px"><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card" style="max-width:560px">
  <form method="POST" action="/index.php?page=salones&action=<?= $accion ?>">
    <?php if ($esEdicion): ?>
      <input type="hidden" name="id" value="<?= $salon['id_salon'] ?>">
    <?php endif; ?>

    <div class="form-grid" style="margin-bottom:16px">

      <div class="form-group form-full">
        <label>Nombre del salón <span style="color:#C0392B">*</span></label>
        <input type="text" name="nombre" required
               placeholder="Ej: Aula 204, Lab. Cómputo, Aula Magna..."
               value="<?= htmlspecialchars($salon['nombre'] ?? $_POST['nombre'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Ubicación <span style="color:#ADB5BD;font-weight:400">(opcional)</span></label>
        <input type="text" name="ubicacion"
               placeholder="Ej: Pabellón A – 2do piso"
               value="<?= htmlspecialchars($salon['ubicacion'] ?? $_POST['ubicacion'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Capacidad <span style="color:#ADB5BD;font-weight:400">(personas, opcional)</span></label>
        <input type="number" name="capacidad" min="1" max="500"
               placeholder="Ej: 30"
               value="<?= htmlspecialchars($salon['capacidad'] ?? $_POST['capacidad'] ?? '') ?>">
      </div>

      <div class="form-group form-full">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:400">
          <input type="checkbox" name="estado" value="1"
                 style="width:16px;height:16px;accent-color:#534AB7"
                 <?= ($salon['estado'] ?? 1) == 1 ? 'checked' : '' ?>>
          <span>Salón disponible — aparece al registrar horarios</span>
        </label>
      </div>

    </div>

    <div class="form-actions">
      <a href="index.php?page=salones" class="btn">Cancelar</a>
      <button type="submit" class="btn btn-primary">
        <i class="ti ti-device-floppy"></i>
        <?= $esEdicion ? 'Guardar cambios' : 'Registrar salón' ?>
      </button>
    </div>
  </form>
</div>
