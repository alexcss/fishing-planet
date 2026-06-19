import React, { useState, useEffect, useCallback, useRef } from 'react'
import DlcGrid from './DlcGrid'
import EmptyState from './EmptyState'
import Pagination from './Pagination'
import LoadingIndicator from './LoadingIndicator'
import type { DlcPost, Filters, DlcApiResponse } from './types'

const PER_PAGE = 3

interface DlcListProps {
  initialPosts: DlcPost[]
  initialTotalPosts?: number
  initialPage?: number
  filters: Filters
  apiEndpoint: string
}

const DlcList: React.FC<DlcListProps> = ({ initialPosts, initialTotalPosts, initialPage = 1, filters, apiEndpoint }) => {
  const [posts, setPosts] = useState<DlcPost[]>(initialPosts)
  const [loading, setLoading] = useState(false)
  const [currentPage, setCurrentPage] = useState(initialPage)
  const [totalPages, setTotalPages] = useState(initialTotalPosts ? Math.ceil(initialTotalPosts / PER_PAGE) : 1)
  const [totalPosts, setTotalPosts] = useState(initialTotalPosts || initialPosts.length)
  const isInitialMount = useRef(true)
  const initialFiltersKey = useRef(
    [filters.category, filters.search, filters.sort, [...filters.include].sort().join(','), [...filters.waterway].sort().join(',')].join('|')
  )

  // Helper to compare arrays
  const arraysEqual = (a: string[], b: string[]): boolean => {
    if (a.length !== b.length) return false
    return [...a].sort().join(',') === [...b].sort().join(',')
  }

  // Update URL with current state (only if changed)
  const updateUrl = useCallback((page: number, filters: Filters) => {
    if (typeof window === 'undefined') return

    const url = new URL(window.location.href)
    const currentCategory = url.searchParams.get('category') || ''
    const currentInclude = url.searchParams.get('include') ? url.searchParams.get('include')!.split(',') : []
    const currentWaterway = url.searchParams.get('waterway') ? url.searchParams.get('waterway')!.split(',') : []
    const currentSearch = url.searchParams.get('search') || ''
    const currentPage = parseInt(url.searchParams.get('pg') || '1', 10)

    // Only update if something changed
    if (
      currentPage === page &&
      currentCategory === (filters.category || '') &&
      arraysEqual(currentInclude, filters.include) &&
      arraysEqual(currentWaterway, filters.waterway) &&
      currentSearch === (filters.search || '')
    ) {
      return
    }

    // Handle arrays - join with commas (manually build to avoid encoding)
    const params: string[] = []
    if (page > 1) params.push(`pg=${page}`)
    if (filters.category) params.push(`category=${encodeURIComponent(filters.category)}`)
    if (filters.include.length > 0) params.push(`include=${filters.include.join(',')}`)
    if (filters.waterway.length > 0) params.push(`waterway=${filters.waterway.join(',')}`)
    if (filters.search) params.push(`search=${encodeURIComponent(filters.search)}`)

    const queryString = params.length > 0 ? `?${params.join('&')}` : ''
    const cleanUrl = `${url.pathname}${queryString}${url.hash}`

    window.history.replaceState({}, '', cleanUrl)
  }, [])

  // Fetch posts
  const fetchPosts = useCallback(
    async (page: number, currentFilters: Filters) => {
      setLoading(true)

      try {
        const params = new URLSearchParams({
          page: page.toString(),
          per_page: PER_PAGE.toString(),
        })

        if (currentFilters.category) params.append('category', currentFilters.category)
        if (currentFilters.include.length > 0) params.append('include', currentFilters.include.join(','))
        if (currentFilters.waterway.length > 0) params.append('waterway', currentFilters.waterway.join(','))
        if (currentFilters.sort) params.append('sort', currentFilters.sort)
        if (currentFilters.search) params.append('search', currentFilters.search)

        const response = await fetch(`${apiEndpoint}?${params}`)
        const data: DlcApiResponse = await response.json()

        setPosts(data.posts)
        setTotalPosts(data.total)
        setTotalPages(Math.ceil(data.total / PER_PAGE))
        setCurrentPage(page)
        updateUrl(page, currentFilters)
      } catch (error) {
        console.error('Error fetching DLCs:', error)
      } finally {
        setLoading(false)
      }
    },
    [apiEndpoint, updateUrl]
  )

  // Handle page change
  const handlePageChange = (page: number) => {
    fetchPosts(page, filters)
    // Scroll to top of list
    const listElement = document.getElementById('dlc-root')
    if (listElement) {
      listElement.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
  }

  // Fetch when filters change (reset to page 1), but skip on initial mount
  useEffect(() => {
    const currentKey = [
      filters.category,
      filters.search,
      filters.sort,
      [...filters.include].sort().join(','),
      [...filters.waterway].sort().join(','),
    ].join('|')

    if (isInitialMount.current) {
      isInitialMount.current = false
      return
    }

    // Skip if filters haven't changed from initial (handles StrictMode double-invoke)
    if (currentKey === initialFiltersKey.current) return

    fetchPosts(1, filters)
  }, [filters, fetchPosts])

  return (
    <div id="dlc-list" className="space-y-32 xl:space-y-60">
      <DlcGrid posts={posts} highlight={filters.search} />

      {posts.length === 0 && !loading && <EmptyState />}

      {!loading && totalPages > 1 && <Pagination currentPage={currentPage} totalPages={totalPages} onPageChange={handlePageChange} />}

      {loading && <LoadingIndicator />}
    </div>
  )
}

export default DlcList
