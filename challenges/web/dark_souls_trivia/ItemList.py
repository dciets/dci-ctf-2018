class ItemList:
    ITEMS = {
        "firebomb": ("Large Titanite Shard", 1),
        "black firebomb": ("Titanite Chunk", 1),
        "prism stone": ("Twinkling Titanite", 1),
        "loretta's bone": ("Ring of Sacrifice", 1),
        "avelyn": ("Titanite Scale", 3),
        "coiled sword fragment": ("Titanite Slab", 1),
        "lightning urn": ("Iron Helm", 1),
        "homeward bone": ("Iron Bracelets", 1),
        "seed of a giant tree": ("Iron Leggings", 1),
        "siegbrau": ("Armor of the Sun", 1),
        "vertebra shackle": ("Lucatiel Mask", 1),
        "divine blessing": ("Very Good Carving", 1),
        "hidden blessing": ("Thank You Carving", 1),
        "alluring skull": ("Hello Carving", 1),
        "undead bone shard": ("Porcine Shield", 1),
        "sacred chime": ("Help Me Carving", 1),
        "shriving stone": ("I'm Sorry Carving", 1),
        "xanthous crown": ("Lightning Gem", 1),
        "mendicant's staff": ("Sunlight Shield", 1),
        "blacksmith hammer": ("Titanite Scale", 1),
        "large leather shield": ("Twinkling Titanite", 1),
        "moaning shield": ("Blessed Gem", 1),
        "eleonora": ("Eleonora", 1)
    }

    @staticmethod
    def check_item(item):
        item = item.lower()
        return ItemList.ITEMS[item] if item in ItemList.ITEMS else None