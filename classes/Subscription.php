<?php
/**
 * Subscription Handler
 */
class Subscription {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function subscribe($email, $type = 'newsletter', $userId = null, $source = 'website') {
        try {
            // Check if already subscribed
            $existing = $this->db->fetchOne(
                "SELECT * FROM subscriptions WHERE email = :email AND subscription_type = :type",
                ['email' => $email, 'type' => $type]
            );
            
            if ($existing) {
                if ($existing['status'] === 'active') {
                    return ['success' => false, 'message' => 'Already subscribed'];
                } else {
                    // Reactivate subscription
                    $unsubscribeToken = bin2hex(random_bytes(32));
                    $this->db->update('subscriptions', [
                        'status' => 'active',
                        'user_id' => $userId,
                        'unsubscribed_at' => null,
                        'unsubscribe_token' => $unsubscribeToken,
                        'source' => $source,
                        'updated_at' => date('Y-m-d H:i:s')
                    ], 'id = :id', ['id' => $existing['id']]);
                    return ['success' => true, 'message' => 'Subscription reactivated'];
                }
            }
            
            // Create new subscription
            $unsubscribeToken = bin2hex(random_bytes(32));
            $subscriptionId = $this->db->insert('subscriptions', [
                'user_id' => $userId,
                'email' => $email,
                'subscription_type' => $type,
                'status' => 'active',
                'unsubscribe_token' => $unsubscribeToken,
                'source' => $source,
                'subscribed_at' => date('Y-m-d H:i:s')
            ]);
            
            return [
                'success' => true,
                'message' => 'Successfully subscribed',
                'id' => $subscriptionId,
                'token' => $unsubscribeToken
            ];
        } catch (Exception $e) {
            error_log("Subscription error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to subscribe: ' . $e->getMessage()];
        }
    }
    
    public function unsubscribe($token) {
        try {
            $subscription = $this->db->fetchOne(
                "SELECT * FROM subscriptions WHERE unsubscribe_token = :token",
                ['token' => $token]
            );
            
            if (!$subscription) {
                return ['success' => false, 'message' => 'Invalid unsubscribe token'];
            }
            
            $this->db->update('subscriptions', [
                'status' => 'unsubscribed',
                'unsubscribed_at' => date('Y-m-d H:i:s')
            ], 'id = :id', ['id' => $subscription['id']]);
            
            return ['success' => true, 'message' => 'Successfully unsubscribed'];
        } catch (Exception $e) {
            error_log("Unsubscribe error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to unsubscribe'];
        }
    }
    
    public function getSubscriptions($userId = null) {
        if ($userId) {
            return $this->db->fetchAll(
                "SELECT * FROM subscriptions WHERE user_id = :user_id ORDER BY subscribed_at DESC",
                ['user_id' => $userId]
            );
        }
        return $this->db->fetchAll(
            "SELECT * FROM subscriptions ORDER BY subscribed_at DESC"
        );
    }
    
    public function getActiveSubscribers($type = 'newsletter') {
        return $this->db->fetchAll(
            "SELECT * FROM subscriptions WHERE subscription_type = :type AND status = 'active' ORDER BY subscribed_at DESC",
            ['type' => $type]
        );
    }
}

