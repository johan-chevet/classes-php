<?php if (isset($errors)): ?>
    <div class="errors">
        <h3>Errors: </h3>
        <?php foreach ($errors as $value): ?>
            <?= $value ?>
            <br>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if (isset($result)): ?>
    <div>
        <h3>Result: </h3>
        <?php foreach ($result as $value): ?>
            <?= $value ?>
            <br>
        <?php endforeach; ?>
    </div>
<?php endif; ?>