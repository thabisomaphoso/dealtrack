import useSWR from 'swr'

const fetcher = url => fetch(url).then(res => res.json())

export default function Products() {
  const { data, error } = useSWR('/api/products', fetcher)

  if (error) return <div>Failed to load</div>
  if (!data) return <div>Loading...</div>

  return (
    <main className="p-8">
      <h1 className="text-2xl font-bold mb-4">Products</h1>
      <ul className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {data.map(product => (
          <li key={product.id} className="p-4 border rounded shadow">
            <h2 className="text-lg font-semibold">{product.name}</h2>
            <p className="text-gray-600">{product.store}</p>
            <p className="font-bold mt-2">R{product.price}</p>
          </li>
        ))}
      </ul>
    </main>
  )
}
