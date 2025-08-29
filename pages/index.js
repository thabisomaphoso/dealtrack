import Link from 'next/link'

export default function Home() {
  return (
    <main className="p-8 text-center">
      <h1 className="text-3xl font-bold">Welcome to DealTrack SA</h1>
      <p className="mt-4">Your smart price comparison platform.</p>
      <Link href="/products" className="mt-6 inline-block px-4 py-2 bg-blue-600 text-white rounded">
        Browse Products
      </Link>
    </main>
  )
}
