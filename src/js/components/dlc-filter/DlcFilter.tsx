import React, { useState, useMemo } from 'react'
import { createRoot } from 'react-dom/client'
import FilterDropdown from './FilterDropdown'
import DlcList from './DlcList'
import MobileFilters from './MobileFilters'
import type { FilterData, Filters, DlcPost } from './types'

interface DlcFilterProps {
  filterData: FilterData
  initialPosts: DlcPost[]
  totalPosts?: number
  initialPage?: number
  initialFilters?: Filters
  apiEndpoint: string
}

const DlcFilter: React.FC<DlcFilterProps> = ({ filterData, initialPosts, totalPosts, initialPage = 1, initialFilters, apiEndpoint }) => {
  const [filters, setFilters] = useState<Filters>(
    initialFilters || {
      category: '',
      include: '',
      waterway: '',
      sort: 'latest',
    }
  )

  // Handle filter changes
  const handleFilterChange = (key: keyof Filters, value: string) => {
    setFilters((prev) => ({
      ...prev,
      [key]: value,
    }))
  }

  // Clear all filters
  const clearFilters = () => {
    setFilters({
      category: '',
      include: '',
      waterway: '',
      sort: 'latest',
    })
  }

  // Count active filters
  const activeFilterCount = useMemo(() => {
    return Object.values(filters).filter((v) => v && v !== 'latest').length
  }, [filters])

  return (
    <div className="space-y-60 xl:space-y-100">
      {/* Desktop Filters */}
      <div className="hidden gap-20 lg:flex xl:gap-40">
        <FilterDropdown
          label="Category"
          options={filterData.categories}
          selected={filters.category}
          onSelect={(value) => handleFilterChange('category', value)}
        />
        <FilterDropdown
          label="Pack Content"
          options={filterData.includes}
          selected={filters.include}
          onSelect={(value) => handleFilterChange('include', value)}
        />
        <FilterDropdown
          label="Waterway"
          options={filterData.waterways}
          selected={filters.waterway}
          onSelect={(value) => handleFilterChange('waterway', value)}
          showSearch
          searchPlaceholder="Search"
        />

        {/* Search Button */}
        <button
          className="bg-gray-gunmetal hover:border-accent flex h-64 items-center justify-center border border-white/15 transition-colors"
          aria-label="Search"
        >
          <svg className="h-24 w-24 text-white" viewBox="0 0 24 24" fill="none">
            <path
              d="M18.031 16.617L22.314 20.899L20.899 22.314L16.617 18.031C15.0237 19.3082 13.042 20.0029 11 20C6.032 20 2 15.968 2 11C2 6.032 6.032 2 11 2C15.968 2 20 6.032 20 11C20.0029 13.042 19.3082 15.0237 18.031 16.617ZM16.025 15.875C17.294 14.57 18.0029 12.82 18 11C18 7.133 14.867 4 11 4C7.133 4 4 7.133 4 11C4 14.867 7.133 18 11 18C12.82 18.0029 14.57 17.294 15.875 16.025L16.025 15.875Z"
              fill="currentColor"
            />
          </svg>
        </button>
      </div>

      {/* Mobile Filters */}
      <MobileFilters
        filterData={filterData}
        filters={filters}
        onFilterChange={handleFilterChange}
        onClearFilters={clearFilters}
        activeFilterCount={activeFilterCount}
      />

      {/* DLC List */}
      <DlcList initialPosts={initialPosts} initialTotalPosts={totalPosts} initialPage={initialPage} filters={filters} apiEndpoint={apiEndpoint} />
    </div>
  )
}

// Mount function for initializing the React app
export function mountDlcFilter(containerId: string, props: DlcFilterProps) {
  const container = document.getElementById(containerId)
  if (!container) {
    console.error(`Container #${containerId} not found`)
    return
  }

  const root = createRoot(container)
  root.render(<DlcFilter {...props} />)
}

export default DlcFilter
