<?php // views/grupos/form.php
$esEdicion = !is_null($grupo);
$accion    = $esEdicion ? 'update' : 'store';
?>

<div class="page-header">
  <div>
    <h1>
      <i class="ti ti-<?= $esEdicion ? 'edit' : 'plus' ?>"
         style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>
      <?= $esEdicion ? 'Editar grupo' : 'Nuevo grupo' ?>
    </h1>
  </div>
  <a href="index.php?page=grupos" class="btn"><i class="ti ti-arrow-left"></i> Volver</a>
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
  <form method="POST" action="index.php?page=grupos&action=<?= $accion ?>">
    <?php if ($esEdicion): ?>
      <input type="hidden" name="id" value="<?= $grupo['id_grupo'] ?>">
    <?php endif; ?>

    <div style="display:flex;flex-direction:column;gap:16px">

      <div class="form-group">
        <label>Nombre del grupo <span style="color:#C0392B">*</span></label>
        <input type="text" name="nombre" required
               placeholder="Ej: Nivel 2, Preselección..."
               value="<?= htmlspecialchars($grupo['nombre'] ?? $_POST['nombre'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Descripción <span style="color:#ADB5BD;font-weight:400">(opcional)</span></label>
        <textarea name="descripcion"
                  placeholder="Breve descripción del grupo..."><?= htmlspecialchars($grupo['descripcion'] ?? $_POST['descripcion'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:400">
          <input type="checkbox" name="estado" value="1"
                 style="width:16px;height:16px;accent-color:#534AB7"
                 <?= ($grupo['estado'] ?? 1) == 1 ? 'checked' : '' ?>>
          <span>Grupo activo</span>
        </label>
      </div>

    </div>

    <div class="form-actions">
      <a href="index.php?page=grupos" class="btn">Cancelar</a>
      <button type="submit" class="btn btn-primary">
        <i class="ti ti-device-floppy"></i>
        <?= $esEdicion ? 'Guardar cambios' : 'Registrar grupo' ?>
      </button>
    </div>
  </form>
</div>