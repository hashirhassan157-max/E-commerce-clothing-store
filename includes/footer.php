<?php
// includes/footer.php
?>
    </main>
    
    <footer class="footer">
    <div class="footer-container">
      <div class="footer-logo-section">
        <img src="images/pj.png" alt="Career Institute Jhang" class="footer-logo">
        <p>PJ Collection: Embracing Humanity and Unity. Explore the PJ Collection celebrating unity and humanity.</p>
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-linkedin-in"></i></a>
          <a href="#"><i class="fab fa-youtube"></i></a>
        </div>
      </div>

      <div class="footer-links-section">
        <h3 class="footer-heading">Important Links</h3>
        <ul class="footer-links">
          <li><a href="#">Home</a></li>
          <li><a href="#">Men's</a></li>
          <li><a href="#">Women's</a></li>
          <li><a href="#">shoes</a></li>
          <li><a href="#">Accessories</a></li>
        </ul>
      </div>

      <div class="footer-links-section">
        <h3 class="footer-heading">Extras</h3>
        <ul class="footer-links">
          <li><a href="#">Tacking order</a></li>
          <li><a href="#">Sales</a></li>
          <li><a href="#">Paymet</a></li>
          <li><a href="#">About us</a></li>
          <li><a href="#">Privacy Policy</a></li>
        </ul>
      </div>

      <div class="footer-contact-section">
        <h3 class="footer-heading">Contact Us</h3>
        <ul class="contact-info">
          <li><i class="fas fa-phone-alt"></i> Landline: 041-8724010</li>
          <li><i class="fab fa-whatsapp"></i> WhatsApp: 0314-4444010</li>
          <li><i class="fas fa-mobile-alt"></i> Hotline: 0341-4444010</li>
          <li><i class="fas fa-envelope"></i> Email: info@career.edu.pk</li>
          <li><i class="fas fa-map-marker-alt"></i> Yousaf Shah Road, near City Center, Jhang</li>
        </ul>
      </div>

      <div class="newsletter">
        <h3 class="footer-heading">Newsletter</h3>
        <p>Subscribe to our newsletter to get updates on our latest programs and events.</p>
        <form class="newsletter-form">
          <input type="email" placeholder="Your Email Address" required>
          <button type="submit"><i class="fas fa-paper-plane"></i></button>
        </form>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; 2025 Pj Collection. All Rights Reserved. | Developed By Hashir Hassan <i class="fas fa-heart"
          style="color: #e25555;"></i></p>
    </div>
  </footer>


    
    <div id="toast-notification" class="toast-notification"></div>

    <script src="js/main.js"></script>
</body>
</html>
<?php
// Close the database connection
if (isset($conn)) {
    $conn->close();
}
?>