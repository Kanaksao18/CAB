<?php
require_once '../includes/config.php';
$page_title = 'Privacy Policy';
ob_start();
?>

<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h1 class="text-3xl font-bold text-gray-900">Privacy Policy</h1>
                <p class="mt-2 text-sm text-gray-500">Last updated: <?php echo date('F d, Y'); ?></p>

                <div class="mt-6 space-y-6 text-gray-700">
                    <section>
                        <h2 class="text-xl font-semibold text-gray-900">1. Information We Collect</h2>
                        <div class="mt-4 space-y-4">
                            <h3 class="text-lg font-medium">Personal Information</h3>
                            <p>We collect information that you provide directly to us, including:</p>
                            <ul class="list-disc pl-5 space-y-2">
                                <li>Name and contact information</li>
                                <li>Account credentials</li>
                                <li>Profile information</li>
                                <li>Payment information</li>
                                <li>Communication preferences</li>
                            </ul>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900">2. How We Use Your Information</h2>
                        <div class="mt-4 space-y-4">
                            <p>We use the information we collect to:</p>
                            <ul class="list-disc pl-5 space-y-2">
                                <li>Provide and maintain our services</li>
                                <li>Process your transactions</li>
                                <li>Send you service-related communications</li>
                                <li>Improve our services</li>
                                <li>Protect against fraud and abuse</li>
                            </ul>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900">3. Information Sharing</h2>
                        <div class="mt-4">
                            <p>We do not sell your personal information. We may share your information with:</p>
                            <ul class="list-disc pl-5 mt-2 space-y-2">
                                <li>Service providers</li>
                                <li>Other users (as necessary for ride sharing)</li>
                                <li>Law enforcement (when required by law)</li>
                            </ul>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900">4. Your Rights</h2>
                        <div class="mt-4">
                            <p>You have the right to:</p>
                            <ul class="list-disc pl-5 mt-2 space-y-2">
                                <li>Access your personal information</li>
                                <li>Correct inaccurate information</li>
                                <li>Request deletion of your information</li>
                                <li>Opt-out of marketing communications</li>
                            </ul>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900">5. Contact Us</h2>
                        <div class="mt-4">
                            <p>If you have any questions about this Privacy Policy, please contact us at:</p>
                            <div class="mt-2">
                                <p>Email: privacy@cabshare.com</p>
                                <p>Phone: 1-800-CABSHARE</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../includes/components/layout.php';
?> 