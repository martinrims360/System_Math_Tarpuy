<?php // views/areas/form.php
$esEdicion = !is_null($area);
$accion    = $esEdicion ? 'update' : 'store';
?>

<div class="page-header">
  <div>
    <h1>
      <i class="ti ti-<?= $esEdicion ? 'edit' : 'tag' ?>"
         style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>
      <?= $esEdicion ? 'Editar área' : 'Nueva área temática' ?>
    </h1>
  </div>
  <a href="index.php?page=areas" class="btn"><i class="ti ti-arrow-left"></i> Volver</a>
</div>

<?php if (!empty($errors)): ?>
  <div style="background:#FAECE7;border-left:4px solid #C0392B;color:#712B13;
              padding:12px 16px;border-radius:10px;margin-bottom:20px">
    <?php foreach ($errors as $e): ?>
      <div style="font-size:13px"><i class="ti ti-alert-circle"></i> <?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="card" style="max-width:480px">
  <form method="POST" action="/index.php?page=areas&action=<?= $accion ?>">
    <?php if ($esEdicion): ?>
      <input type="hidden" name="id" value="<?= $area['id_area'] ?>">
    <?php endif; ?>

    <div class="form-group" style="margin-bottom:20px">
      <label>Nombre del área <span style="color:#C0392B">*</span></label>
      <input type="text" name="nombre" required autofocus
             placeholder="Ej: Álgebra, Geometría, Combinatoria..."
             value="<?= htmlspecialchars($area['nombre'] ?? $_POST['nombre'] ?? '') ?>">
      <span style="font-size:12px;color:#ADB5BD;margin-top:4px">
        Ejemplos: Álgebra · Geometría · Teoría de Números · Combinatoria · Lógica Matemática
      </span>
    </div>

    <div class="form-actions">
      <a href="index.php?page=areas" class="btn">Cancelar</a>
      <button type="submit" class="btn btn-primary">
        <i class="ti ti-device-floppy"></i>
        <?= $esEdicion ? 'Guardar cambios' : 'Registrar área' ?>
      </button>
    </div>
  </form>
</div>
