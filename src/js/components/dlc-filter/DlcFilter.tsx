import React, { useState, useMemo } from 'react'
import { createRoot } from 'react-dom/client'
import FilterDropdown from './FilterDropdown'
import DlcList from './DlcList'
import MobileFilters from './MobileFilters'
import DesktopSearch from './DesktopSearch'
import WaterwayFilter from './WaterwayFilter'
import type { FilterData, Filters, DlcPost, AvailableTerms } from './types'

interface DlcFilterProps {
  filterData: FilterData
  initialPosts: DlcPost[]
  totalPosts?: number
  initialPage?: number
  initialFilters?: Filters
  apiEndpoint: string
}

const DlcFilter: React.FC<DlcFilterProps> = ({ filterData, initialPosts, totalPosts, initialPage = 1, initialFilters, apiEndpoint }) => {
  const [availableTerms, setAvailableTerms] = useState<AvailableTerms | null>(null)

  const [filters, setFilters] = useState<Filters>({
    category: '',
    include: [],
    waterway: [],
    fishing_style: [],
    sort: 'latest',
    search: '',
    ...initialFilters,
  })

  // Handle filter changes
  const handleFilterChange = (key: keyof Filters, value: string | string[]) => {
    setFilters((prev) => ({
      ...prev,
      [key]: value,
    }))
  }

  // Clear all filters
  const clearFilters = () => {
    setFilters({
      category: '',
      include: [],
      waterway: [],
      fishing_style: [],
      sort: 'latest',
      search: '',
    })
  }

  // Count active filters
  const activeFilterCount = useMemo(() => {
    let count = 0
    if (filters.category) count++
    if (filters.include.length > 0) count += filters.include.length
    if (filters.waterway.length > 0) count += filters.waterway.length
    if (filters.fishing_style.length > 0) count += filters.fishing_style.length
    return count
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
          availableSlugs={availableTerms?.categories}
        />
        <FilterDropdown
          label="Fishing Style"
          options={filterData.fishing_styles}
          selected={filters.fishing_style}
          onSelect={(value) => handleFilterChange('fishing_style', value as string[])}
          availableSlugs={availableTerms?.fishing_styles}
          multi
        />
        <WaterwayFilter
          label="Waterway"
          options={filterData.waterways}
          selected={filters.waterway}
          onChange={(value) => handleFilterChange('waterway', value)}
          availableSlugs={availableTerms?.waterways}
        />
        <FilterDropdown
          label="Pack Content"
          options={filterData.includes}
          selected={filters.include}
          onSelect={(value) => handleFilterChange('include', value as string[])}
          availableSlugs={availableTerms?.includes}
          multi
        />

        <DesktopSearch value={filters.search} onChange={(value) => handleFilterChange('search', value)} />
      </div>

      {/* Mobile Filters */}
      <MobileFilters
        filterData={filterData}
        filters={filters}
        onFilterChange={handleFilterChange}
        onSearchChange={(value) => handleFilterChange('search', value)}
        onClearFilters={clearFilters}
        activeFilterCount={activeFilterCount}
        availableTerms={availableTerms}
      />

      {/* DLC List */}
      <DlcList
        initialPosts={initialPosts}
        initialTotalPosts={totalPosts}
        initialPage={initialPage}
        filters={filters}
        apiEndpoint={apiEndpoint}
        onAvailableTermsChange={setAvailableTerms}
      />
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
