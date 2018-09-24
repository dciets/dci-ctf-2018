from collections import defaultdict
from datetime import datetime

class UserData:
    def __init__(self, submitted_items=[], prizes=defaultdict(int)):
        self.submitted_items = submitted_items
        self.prizes = prizes

    def add_prize(self, submitted_item, prize, count):
        self.submitted_items.append(submitted_item)
        self.prizes[prize] += count

    def has_submitted(self, item):
        return item in self.submitted_items

    def __reduce__(self):
        return UserData, (self.submitted_items, self.prizes)