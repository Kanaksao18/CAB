<footer class="bg-gray-800 text-white mt-auto">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- About Section -->
            <div class="col-span-1 md:col-span-2">
                <h3 class="text-xl font-semibold mb-4">About CabShare</h3>
                <p class="text-gray-300">
                    CabShare is a platform that connects drivers and passengers for shared rides.
                    Save money, reduce traffic, and help the environment by sharing your journey.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-xl font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="<?php echo BASE_URL; ?>/public/about.php" 
                           class="text-gray-300 hover:text-white">About Us</a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/public/contact.php" 
                           class="text-gray-300 hover:text-white">Contact Us</a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/public/privacy.php" 
                           class="text-gray-300 hover:text-white">Privacy Policy</a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/public/terms.php" 
                           class="text-gray-300 hover:text-white">Terms of Service</a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h3 class="text-xl font-semibold mb-4">Contact Us</h3>
                <ul class="space-y-2">
                    <li class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        <a href="mailto:support@cabshare.com" 
                           class="text-gray-300 hover:text-white">support@cabshare.com</a>
                    </li>
                    <li class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                        </svg>
                        <span class="text-gray-300">+1 (555) 123-4567</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-700 mt-8 pt-8 text-center">
            <p class="text-gray-300">
                © <?php echo date('Y'); ?> CabShare. All rights reserved.
            </p>
        </div>
    </div>
</footer> 