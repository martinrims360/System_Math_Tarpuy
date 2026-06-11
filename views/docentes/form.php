<?php // views/docentes/form.php
$esEdicion = !is_null($docente);
$accion    = $esEdicion ? 'update' : 'store';
?>

<div class="page-header">
  <div>
    <h1>
      <i class="ti ti-<?= $esEdicion ? 'edit' : 'user-plus' ?>"
         style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>
      <?= $esEdicion ? 'Editar docente' : 'Nuevo docente' ?>
    </h1>
    <p><?= $esEdicion ? 'Modifica los datos del docente' : 'Completa el formulario para registrar un docente' ?></p>
  </div>
  <a href="/index.php?page=docentes" class="btn">
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
  <form method="POST"
        action="/index.php?page=docentes&action=<?= $accion ?>">

    <?php if ($esEdicion): ?>
      <input type="hidden" name="id" value="<?= $docente['id_docente'] ?>">
    <?php endif; ?>

    <div class="form-grid">

      <!-- Nombre -->
      <div class="form-group form-full">
        <label>Nombre completo <span style="color:#C0392B">*</span></label>
        <input type="text" name="nombre" required
               placeholder="Ej: Martín Rimachi Quispe"
               value="<?= htmlspecialchars($docente['nombre'] ?? $_POST['nombre'] ?? '') ?>">
      </div>

      <!-- Correo -->
      <div class="form-group">
        <label>Correo institucional <span style="color:#C0392B">*</span></label>
        <input type="email" name="correo" required
               placeholder="docente@colegio.edu.pe"
               value="<?= htmlspecialchars($docente['correo'] ?? $_POST['correo'] ?? '') ?>">
      </div>

      <!-- Rol -->
      <div class="form-group">
        <label>Rol <span style="color:#C0392B">*</span></label>
        <select name="rol" required>
          <option value="">Seleccionar...</option>
          <option value="docente"
            <?= ($docente['rol'] ?? $_POST['rol'] ?? '') === 'docente' ? 'selected' : '' ?>>
            Docente
          </option>
          <option value="coordinador"
            <?= ($docente['rol'] ?? $_POST['rol'] ?? '') === 'coordinador' ? 'selected' : '' ?>>
            Coordinador
          </option>
        </select>
      </div>

      <!-- Contraseña -->
      <div class="form-group">
        <label>
          Contraseña
          <?php if (!$esEdicion): ?>
            <span style="color:#C0392B">*</span>
          <?php else: ?>
            <span style="font-weight:400;color:#ADB5BD">(dejar en blanco para no cambiar)</span>
          <?php endif; ?>
        </label>
        <input type="password" name="password"
               placeholder="Mínimo 6 caracteres"
               <?= !$esEdicion ? 'required' : '' ?>>
      </div>

      <!-- Estado -->
      <div class="form-group form-full">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;
                      user-select:none;font-weight:400">
          <input type="checkbox" name="estado" value="1"
                 style="width:16px;height:16px;accent-color:#534AB7"
                 <?= ($docente['estado'] ?? 1) == 1 ? 'checked' : '' ?>>
          <span>Cuenta activa — el docente puede iniciar sesión</span>
        </label>
      </div>

    </div><!-- /form-grid -->

    <div class="form-actions">
      <a href="/index.php?page=docentes" class="btn">Cancelar</a>
      <button type="submit" class="btn btn-primary">
        <i class="ti ti-<?= $esEdicion ? 'device-floppy' : 'user-plus' ?>"></i>
        <?= $esEdicion ? 'Guardar cambios' : 'Registrar docente' ?>
      </button>
    </div>

  </form>
</div>