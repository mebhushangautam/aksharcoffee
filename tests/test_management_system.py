import unittest

from management_system import CoffeeRestaurantManagementSystem, OrderType


class TestCoffeeRestaurantManagementSystem(unittest.TestCase):
    def test_add_menu_item_rejects_invalid_values(self):
        system = CoffeeRestaurantManagementSystem()

        with self.assertRaises(ValueError):
            system.add_menu_item("", 10)
        with self.assertRaises(ValueError):
            system.add_menu_item("Espresso", -1)

    def test_dine_in_order_requires_table_and_tracks_assignment(self):
        system = CoffeeRestaurantManagementSystem()

        with self.assertRaises(ValueError):
            system.create_order(OrderType.DINE_IN)

        order = system.create_order(OrderType.DINE_IN, table_number=4)
        self.assertEqual(system.table_assignments[4], order.order_id)

    def test_bill_calculation_with_tax(self):
        system = CoffeeRestaurantManagementSystem(tax_rate=0.10)
        system.add_menu_item("Latte", 4.50)
        system.add_menu_item("Sandwich", 6.00)

        order = system.create_order(OrderType.TAKEAWAY)
        system.add_item_to_order(order.order_id, "Latte")
        system.add_item_to_order(order.order_id, "Sandwich")

        bill = system.generate_bill(order.order_id)
        self.assertEqual(bill, {"subtotal": 10.50, "tax": 1.05, "total": 11.55})

    def test_paid_order_cannot_be_modified(self):
        system = CoffeeRestaurantManagementSystem()
        system.add_menu_item("Cappuccino", 3.75)
        order = system.create_order(OrderType.TAKEAWAY)
        system.mark_order_paid(order.order_id)

        with self.assertRaises(ValueError):
            system.add_item_to_order(order.order_id, "Cappuccino")


if __name__ == "__main__":
    unittest.main()
