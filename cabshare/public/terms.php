<?php
require_once '../includes/config.php';
$page_title = 'Terms & Conditions';
ob_start();
?>

<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h1 class="text-3xl font-bold text-gray-900">Terms & Conditions</h1>
                <p class="mt-2 text-sm text-gray-500">Last updated: <?php echo date('F d, Y'); ?></p>

                <div class="mt-6 space-y-6 text-gray-700">
                    <section>
                        <h2 class="text-xl font-semibold text-gray-900">1. Acceptance of Terms</h2>
                        <div class="mt-4">
                            <p>By accessing and using CabShare, you accept and agree to be bound by these Terms and Conditions 
                               and our Privacy Policy. If you do not agree to these terms, please do not use our service.</p>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900">2. User Responsibilities</h2>
                        <div class="mt-4 space-y-4">
                            <h3 class="text-lg font-medium">2.1 Account Creation</h3>
                            <ul class="list-disc pl-5 space-y-2">
                                <li>You must provide accurate and complete information</li>
                                <li>You are responsible for maintaining account security</li>
                                <li>You must be at least 18 years old</li>
                                <li>One account per person</li>
                            </ul>

                            <h3 class="text-lg font-medium">2.2 User Conduct</h3>
                            <ul class="list-disc pl-5 space-y-2">
                                <li>Treat other users with respect</li>
                                <li>Provide accurate ride information</li>
                                <li>Follow local laws and regulations</li>
                                <li>Maintain vehicle safety and cleanliness (for drivers)</li>
                            </ul>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900">3. Service Rules</h2>
                        <div class="mt-4 space-y-4">
                            <h3 class="text-lg font-medium">3.1 Booking and Cancellation</h3>
                            <ul class="list-disc pl-5 space-y-2">
                                <li>Rides must be booked through the platform</li>
                                <li>Cancellation policies must be followed</li>
                                <li>Payment terms must be respected</li>
                            </ul>

                            <h3 class="text-lg font-medium">3.2 Safety Guidelines</h3>
                            <ul class="list-disc pl-5 space-y-2">
                                <li>Verify user identities</li>
                                <li>Share ride details with trusted contacts</li>
                                <li>Follow COVID-19 safety protocols</li>
                            </ul>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900">4. Liability</h2>
                        <div class="mt-4">
                            <p>CabShare is not liable for:</p>
                            <ul class="list-disc pl-5 mt-2 space-y-2">
                                <li>Actions of users</li>
                                <li>Vehicle condition or maintenance</li>
                                <li>Accidents or incidents during rides</li>
                                <li>Lost or damaged property</li>
                            </ul>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900">5. Termination</h2>
                        <div class="mt-4">
                            <p>We reserve the right to:</p>
                            <ul class="list-disc pl-5 mt-2 space-y-2">
                                <li>Suspend or terminate accounts</li>
                                <li>Modify or discontinue services</li>
                                <li>Update these terms at any time</li>
                            </ul>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900">6. Contact Information</h2>
                        <div class="mt-4">
                            <p>For questions about these terms, contact us at:</p>
                            <div class="mt-2">
                                <p>Email: support@cabshare.com</p>
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