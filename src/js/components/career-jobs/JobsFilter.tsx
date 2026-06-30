import React, { useState, useMemo } from 'react'
import JobFilterDropdown from './JobFilterDropdown'
import JobsMobileFilters from './JobsMobileFilters'
import JobsList from './JobsList'
import MobileSearchButton from '../dlc-filter/MobileSearchButton'
import type { JobFilterData, JobFilters, JobPost } from './types'

interface JobsFilterProps {
  filterData: JobFilterData
  initialJobs: JobPost[]
  initialTotal: number
  initialPage?: number
  perPage: number
  apiEndpoint: string
}

const JobsFilter: React.FC<JobsFilterProps> = ({ filterData, initialJobs, initialTotal, initialPage = 1, perPage, apiEndpoint }) => {
  const [filters, setFilters] = useState<JobFilters>({
    department: '',
    location: '',
    search: '',
  })

  const handleFilterChange = (key: keyof JobFilters, value: string) => {
    setFilters((prev) => ({ ...prev, [key]: value }))
  }

  const clearFilters = () => {
    setFilters({ department: '', location: '', search: '' })
  }

  const activeFilterCount = useMemo(() => {
    let count = 0
    if (filters.department) count++
    if (filters.location) count++
    if (filters.search) count++
    return count
  }, [filters])

  return (
    <div className="space-y-40 md:space-y-60 xl:space-y-80">
      <div className="hidden items-center gap-20 lg:grid lg:grid-cols-3 xl:gap-40">
        <MobileSearchButton value={filters.search} onChange={(value) => handleFilterChange('search', value)} />

        {filterData.departments.length > 0 && (
          <JobFilterDropdown
            label="Department"
            options={filterData.departments}
            selected={filters.department}
            onSelect={(value) => handleFilterChange('department', value)}
          />
        )}

        {filterData.locations.length > 0 && (
          <JobFilterDropdown
            label="Location"
            options={filterData.locations}
            selected={filters.location}
            onSelect={(value) => handleFilterChange('location', value)}
          />
        )}
      </div>

      <JobsMobileFilters
        filterData={filterData}
        filters={filters}
        onFilterChange={handleFilterChange}
        onSearchChange={(value) => handleFilterChange('search', value)}
        onClearFilters={clearFilters}
        activeFilterCount={activeFilterCount}
      />

      <JobsList
        initialJobs={initialJobs}
        initialTotal={initialTotal}
        initialPage={initialPage}
        perPage={perPage}
        filters={filters}
        apiEndpoint={apiEndpoint}
      />
    </div>
  )
}

export default JobsFilter
