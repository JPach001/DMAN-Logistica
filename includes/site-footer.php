<?php
$pageScripts = $pageScripts ?? [];
$extraFooterHtml = $extraFooterHtml ?? '';
?>
<footer class="footer">
    <div class="columna-footer">
        <br>Acerca de<br><br>
        <a class="a_footer" href="Acerca_de_nosotros.php">Quiénes somos</a><br>
        Misión y visión<br>
        <a class="a_footer" href="Servicios.php">Servicios</a><br>
        Mudanzas residenciales<br>
        Mudanzas de oficinas<br>
        Fletes nacionales<br>
    </div>
    <div class="columna-footer">
        <br>Enlaces<br><br>
        <a class="a_footer" href="Preguntas_frecuentes.php">Preguntas frecuentes (FAQ)</a><br>
        <a class="a_footer" href="formulario_cotizar.php">Cotizar en línea</a><br>
        Rastrear mi envío<br>
        Términos y condiciones<br>
        Política de privacidad<br>
        Aviso legal<br>
    </div>
    <div class="columna-footer">
        <br>Contacto<br><br>
        <span>📞 Teléfono: (442) 567-0416</span><br>
        <span>📧 Email: dmanlogistica@gmail.com</span><br>
        <span>📍 Dirección: Escarcha 146 Col. Satelite, Querétaro, Mexico</span><br>
    </div>
    <div class="columna-footer">
        <br>Síguenos<br><br>
        <a href="https://www.facebook.com/profile.php?id=61579422893176" target="_blank" rel="noreferrer">
            <img width="20" src="img/img_facebook.png" alt="Facebook">
        </a>
        <a href="https://wa.me/4425670416" target="_blank" rel="noreferrer">
            <img width="20" src="img/img_whats.png" alt="WhatsApp">
        </a><br>
    </div>
</footer>

<?php echo $extraFooterHtml; ?>

<?php foreach ($pageScripts as $script): ?>
    <?php if (is_array($script)): ?>
        <script src="<?php echo htmlspecialchars((string) ($script['src'] ?? '')); ?>"<?php echo isset($script['attrs']) ? ' ' . $script['attrs'] : ''; ?>></script>
    <?php else: ?>
        <script src="<?php echo htmlspecialchars((string) $script); ?>"></script>
    <?php endif; ?>
<?php endforeach; ?>
</body>
</html>
