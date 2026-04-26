-- Update landlord_profiles to store subaccount and bank info
ALTER TABLE landlord_profiles 
ADD COLUMN subaccount_code VARCHAR(100) NULL,
ADD COLUMN bank_name VARCHAR(100) NULL,
ADD COLUMN account_number VARCHAR(20) NULL,
ADD COLUMN bank_code VARCHAR(10) NULL;

-- Update transactions status ENUM
ALTER TABLE transactions 
MODIFY COLUMN status ENUM('pending', 'completed', 'failed', 'escrow_hold', 'released', 'refunded') DEFAULT 'pending';

-- Add paystack reference to transactions
ALTER TABLE transactions 
ADD COLUMN paystack_reference VARCHAR(100) NULL,
ADD COLUMN payout_status ENUM('pending', 'paid') DEFAULT 'pending';

-- Add unique constraint to paystack_reference to prevent duplicate processing
ALTER TABLE transactions 
ADD UNIQUE INDEX idx_paystack_ref (paystack_reference);
