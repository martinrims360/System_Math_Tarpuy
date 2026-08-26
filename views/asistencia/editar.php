<?php // views/asistencia/editar.php ?>

<div class="page-header">
  <div>
    <h1><i class="ti ti-edit" style="color:#534AB7;vertical-align:-3px;margin-right:8px"></i>Editar Asistencia</h1>
    <p><?= htmlspecialchars($docenteNombre) ?> — <?= date('d/m/Y', strtotime($registro['fecha'])) ?></p>
  </div>
  <a href="index.php?page=asistencia" class="btn btn-sm">
    <i class="ti ti-arrow-left"></i> Volver
  </a>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger" style="margin-bottom:16px">
    <ul style="margin:0;padding-left:18px">
      <?php foreach ($errors as $err): ?>
        <li><?= htmlspecialchars($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card">
  <form action="index.php?page=asistencia&action=update" method="POST">
    <input type="hidden" name="id" value="<?= $registro['id_asistencia'] ?>">

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:14px">
      <div>
        <label style="font-size:12.5px;color:#495057;font-weight:500">Fecha</label>
        <input type="text" class="form-control" value="<?= date('d/m/Y', strtotime($registro['fecha'])) ?>" disabled>
      </div>
      <div>
        <label style="font-size:12.5px;color:#495057;font-weight:500">Hora de entrada</label>
        <input type="time" name="hora_entrada" class="form-control"
               value="<?= htmlspecialchars($registro['hora_entrada'] ?? '') ?>">
      </div>
      <div>
        <label style="font-size:12.5px;color:#495057;font-weight:500">Hora de salida</label>
        <input type="time" name="hora_salida" class="form-control"
               value="<?= htmlspecialchars($registro['hora_salida'] ?? '') ?>">
      </div>
      <div>
        <label style="font-size:12.5px;color:#495057;font-weight:500">Estado</label>
        <select name="estado" class="form-control" required>
          <?php foreach ($estados as $key => $e): ?>
            <option value="<?= $key ?>" <?= $registro['estado'] === $key ? 'selected' : '' ?>><?= $e['label'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div style="margin-bottom:18px">
      <label style="font-size:12.5px;color:#495057;font-weight:500">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($registro['observaciones'] ?? '') ?></textarea>
    </div>

    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary">
        <i class="ti ti-device-floppy"></i> Guardar cambios
      </button>
      <a href="index.php?page=asistencia" class="btn btn-sm">Cancelar</a>
    </div>
  </form>
</div>