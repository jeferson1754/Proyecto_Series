<div class="modal fade" id="edit<?php echo $mostrar[$fila7]; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content border-0 shadow-lg rounded-lg">
      <div class="modal-header bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
        <h5 class="modal-title d-flex align-items-center fw-bold">
          <i class="fas fa-edit me-2"></i>Actualizar Webtoon
        </h5>
        <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST" action="recib_Update.php">
        <?php include('regreso-modal.php');  ?>
        <input type="hidden" name="id" value="<?php echo $mostrar['ID']; ?>">
        <input type="hidden" name="estado_antiguo" value="<?php echo $mostrar[$fila8]; ?>">

        <div class="modal-body p-4">
          <div class="form-group">
            <label for="recipient-name" class="col-form-label"><?php echo $fila1 ?></label>
            <input type="text" name="fila1" class="form-control" value="<?php echo $mostrar[$fila1]; ?>" required="true">
          </div>
          <div class="row g-3">
            <!-- Autor -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label fw-bold"><?php echo $fila2 ?></label>
                <input type="text" name="fila2" class="form-control" value="<?php echo $mostrar[$fila2]; ?>">
              </div>
            </div>

            <!-- Estado Link -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label fw-bold"><?php echo $titulo1 ?></label>
                <select name="fila11" class="form-select" required>
                  <?php
                  $query = "SELECT $fila8 FROM `$tabla2`;";
                  $stmt = $db->prepare($query);
                  $stmt->execute();
                  $estados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  if (empty($mostrar[$fila11])) {
                    echo "<option value=''>Selecciona un Estado Link</option>";
                  }

                  if ($estados) {
                    foreach ($estados as $estado) {
                      echo "<option value='{$estado[$fila8]}' " .
                        ($estado[$fila8] === $mostrar[$fila11] ? 'selected' : '') .
                        ">{$estado[$fila8]}</option>";
                    }
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- Capítulos -->
            <div class="col-md-3">
              <div class="form-group">
                <label class="form-label fw-bold"><?php echo $fila3 ?></label>
                <input type="number" name="fila3" class="form-control" max="<?php echo $mostrar['Total']; ?>" value="<?php echo $mostrar[$fila3]; ?>">
              </div>
            </div>

            <!-- Total -->
            <div class="col-md-3">
              <div class="form-group">
                <label class="form-label fw-bold">Totales</label>
                <input type="number" name="totales" class="form-control" value="<?php echo $mostrar['Total']; ?>">
              </div>
            </div>

            <!-- Temporadas -->
            <div class="col-md-3">
              <div class="form-group">
                <label class="form-label fw-bold"><?php echo $fila4 ?></label>
                <input type="number" name="fila4" class="form-control" value="<?php echo $mostrar[$fila4]; ?>">
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label class="form-label fw-bold">Total de Temporadas</label>
                <input type="number" name="temp_totales" class="form-control" value="<?= $mostrar['Temp_Totales'] ?? 1; ?>">
              </div>
            </div>

            <!-- Estado -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label fw-bold"><?php echo $fila8 ?></label>
                <select name="fila8" class="form-select" required>
                  <?php
                  $query = "SELECT * FROM estado;";
                  $stmt = $db->prepare($query);
                  $stmt->execute();
                  $estados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  if (empty($mostrar[$fila8])) {
                    echo "<option value=''>Selecciona un estado</option>";
                  }

                  if ($estados) {
                    foreach ($estados as $estado) {
                      echo "<option value='{$estado['Estado']}' " .
                        ($estado['Estado'] === $mostrar[$fila8] ? 'selected' : '') .
                        ">{$estado['Estado']}</option>";
                    }
                  }
                  ?>
                </select>

              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label fw-bold"><?php echo $fila6 ?></label>
                <select name="fila6" class="form-select" required>
                  <?php
                  $query = "SELECT * FROM $tabla5;";
                  $stmt = $db->prepare($query);
                  $stmt->execute();
                  $estados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  if (empty($mostrar[$fila6])) {
                    echo "<option value=''>Selecciona un estado</option>";
                  }

                  if ($estados) {
                    foreach ($estados as $estado) {
                      echo "<option value='{$estado['Dia']}' " .
                        ($estado['Dia'] === $mostrar[$fila6] ? 'selected' : '') .
                        ">{$estado['Dia']}</option>";
                    }
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- Capítulos -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label fw-bold">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $mostrar['Fecha_Inicio']; ?>">
              </div>
            </div>

            <!-- Total -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label fw-bold">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="<?php echo $mostrar['Fecha_Fin']; ?>">
              </div>
            </div>
          </div>



        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
      </form>

    </div>
  </div>
</div>
<!---fin ventana Update --->