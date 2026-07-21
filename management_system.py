from dataclasses import dataclass, field
from enum import Enum
from typing import Dict, List


class OrderType(str, Enum):
    DINE_IN = "dine-in"
    TAKEAWAY = "takeaway"


@dataclass(frozen=True)
class MenuItem:
    name: str
    price: float


@dataclass
class Order:
    order_id: int
    order_type: OrderType
    items: List[MenuItem] = field(default_factory=list)
    is_paid: bool = False

    @property
    def subtotal(self) -> float:
        return round(sum(item.price for item in self.items), 2)


class CoffeeRestaurantManagementSystem:
    def __init__(self, tax_rate: float = 0.0) -> None:
        if tax_rate < 0:
            raise ValueError("tax_rate cannot be negative")
        self.tax_rate = tax_rate
        self.menu: Dict[str, MenuItem] = {}
        self.orders: Dict[int, Order] = {}
        self.table_assignments: Dict[int, int] = {}
        self._next_order_id = 1

    def add_menu_item(self, name: str, price: float) -> MenuItem:
        if not name.strip():
            raise ValueError("name is required")
        if price < 0:
            raise ValueError("price cannot be negative")
        item = MenuItem(name=name.strip(), price=round(price, 2))
        self.menu[item.name] = item
        return item

    def create_order(self, order_type: OrderType, table_number: int | None = None) -> Order:
        order = Order(order_id=self._next_order_id, order_type=order_type)
        self.orders[order.order_id] = order
        self._next_order_id += 1

        if order_type == OrderType.DINE_IN:
            if table_number is None or table_number <= 0:
                raise ValueError("valid table_number is required for dine-in orders")
            self.table_assignments[table_number] = order.order_id

        return order

    def add_item_to_order(self, order_id: int, item_name: str) -> None:
        order = self._require_order(order_id)
        if order.is_paid:
            raise ValueError("cannot modify a paid order")
        if item_name not in self.menu:
            raise ValueError(f"menu item '{item_name}' not found")
        order.items.append(self.menu[item_name])

    def generate_bill(self, order_id: int) -> Dict[str, float]:
        order = self._require_order(order_id)
        subtotal = order.subtotal
        tax = round(subtotal * self.tax_rate, 2)
        total = round(subtotal + tax, 2)
        return {"subtotal": subtotal, "tax": tax, "total": total}

    def mark_order_paid(self, order_id: int) -> None:
        order = self._require_order(order_id)
        order.is_paid = True

    def _require_order(self, order_id: int) -> Order:
        if order_id not in self.orders:
            raise ValueError(f"order '{order_id}' not found")
        return self.orders[order_id]
