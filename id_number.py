class Account:
    last_id = 100002  # Last registered account ID number
    
    def __init__(self, account_type):
        self.account_type = account_type
        self.id_number = self.generate_id_number()

    @classmethod
    def generate_id_number(cls):
        cls.last_id += 1
        return cls.last_id