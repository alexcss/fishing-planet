import React, { useState, useEffect, useCallback, useRef } from 'react'
import JobsGrid from './JobsGrid'
import Pagination from '../dlc-filter/Pagination'
import LoadingIndicator from '../dlc-filter/LoadingIndicator'
import EmptyState from '../dlc-filter/EmptyState'
import type { JobPost, JobFilters, JobApiResponse } from './types'

interface JobsListProps {
  initialJobs: JobPost[]
  initialTotal: number
  initialPage?: number
  perPage: number
  filters: JobFilters
  apiEndpoint: string
}

const JobsList: React.FC<JobsListProps> = ({ initialJobs, initialTotal, initialPage = 1, perPage, filters, apiEndpoint }) => {
  const [jobs, setJobs] = useState<JobPost[]>(initialJobs)
  const [loading, setLoading] = useState(false)
  const [currentPage, setCurrentPage] = useState(initialPage)
  const [totalPages, setTotalPages] = useState(Math.ceil(initialTotal / perPage) || 1)
  const isInitialMount = useRef(true)
  const prevFiltersKey = useRef<string | null>(null)

  const fetchJobs = useCallback(
    async (page: number, currentFilters: JobFilters) => {
      setLoading(true)

      try {
        const params = new URLSearchParams({
          page: page.toString(),
          per_page: perPage.toString(),
        })

        if (currentFilters.department) params.append('department', currentFilters.department)
        if (currentFilters.location) params.append('location', currentFilters.location)
        if (currentFilters.search) params.append('search', currentFilters.search)

        const response = await fetch(`${apiEndpoint}?${params}`)
        const data: JobApiResponse = await response.json()

        setJobs(data.posts)
        setTotalPages(Math.ceil(data.total / perPage) || 1)
        setCurrentPage(page)
      } catch (error) {
        console.error('Error fetching careers:', error)
      } finally {
        setLoading(false)
      }
    },
    [apiEndpoint, perPage]
  )

  const handlePageChange = (page: number) => {
    fetchJobs(page, filters)
    const section = document.getElementById('career-jobs-section')
    if (section) {
      section.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
  }

  useEffect(() => {
    const currentKey = [filters.department, filters.location, filters.search].join('|')

    if (isInitialMount.current) {
      isInitialMount.current = false
      prevFiltersKey.current = currentKey
      return
    }

    if (currentKey === prevFiltersKey.current) return

    prevFiltersKey.current = currentKey
    fetchJobs(1, filters)
  }, [filters, fetchJobs])

  return (
    <div className="relative space-y-32 md:space-y-40 xl:space-y-60">
      {loading && <LoadingIndicator className="xl:pb-15!" />}

      <JobsGrid jobs={jobs} />

      {jobs.length === 0 && !loading && <EmptyState />}

      {!loading && totalPages > 1 && <Pagination currentPage={currentPage} totalPages={totalPages} onPageChange={handlePageChange} />}
    </div>
  )
}

export default JobsList
