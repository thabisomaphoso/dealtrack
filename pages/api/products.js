export default function handler(req, res) {
  res.status(200).json([
    { id: 1, name: "Pampers Premium", store: "Store A", price: 250 },
    { id: 2, name: "Huggies Gold", store: "Store B", price: 230 },
    { id: 3, name: "Baby Soft Toilet Paper", store: "Store C", price: 90 }
  ])
}
