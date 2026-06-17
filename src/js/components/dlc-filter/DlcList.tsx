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

  // Update URL with current state (only if changed)
  const updateUrl = useCallback((page: number, filters: Filters) => {
    if (typeof window === 'undefined') return

    const url = new URL(window.location.href)
    const currentCategory = url.searchParams.get('category') || ''
    const currentInclude = url.searchParams.get('include') || ''
    const currentWaterway = url.searchParams.get('waterway') || ''
    const currentPage = parseInt(url.searchParams.get('page') || '1', 10)

    // Only update if something changed
    if (
      currentPage === page &&
      currentCategory === (filters.category || '') &&
      currentInclude === (filters.include || '') &&
      currentWaterway === (filters.waterway || '')
    ) {
      return
    }

    // Update page
    if (page > 1) {
      url.searchParams.set('page', page.toString())
    } else {
      url.searchParams.delete('page')
    }

    // Update filters
    if (filters.category) {
      url.searchParams.set('category', filters.category)
    } else {
      url.searchParams.delete('category')
    }

    if (filters.include) {
      url.searchParams.set('include', filters.include)
    } else {
      url.searchParams.delete('include')
    }

    if (filters.waterway) {
      url.searchParams.set('waterway', filters.waterway)
    } else {
      url.searchParams.delete('waterway')
    }

    window.history.replaceState({}, '', url.toString())
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
        if (currentFilters.include) params.append('include', currentFilters.include)
        if (currentFilters.waterway) params.append('waterway', currentFilters.waterway)
        if (currentFilters.sort) params.append('sort', currentFilters.sort)

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
    if (isInitialMount.current) {
      isInitialMount.current = false
      return
    }
    fetchPosts(1, filters)
  }, [filters, fetchPosts])

  return (
    <div id="dlc-list" className="space-y-32 xl:space-y-60">
      <DlcGrid posts={posts} />

      {posts.length === 0 && !loading && <EmptyState />}

      {!loading && totalPages > 1 && <Pagination currentPage={currentPage} totalPages={totalPages} onPageChange={handlePageChange} />}

      {loading && <LoadingIndicator />}
    </div>
  )
}

export default DlcList
