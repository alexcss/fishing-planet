import React, { useState } from 'react'
import type { JobFilterData, JobFilters } from './types'
import JobMobileFilterAccordion from './JobMobileFilterAccordion'
import MobileSearchButton from '../dlc-filter/MobileSearchButton'

interface JobsMobileFiltersProps {
  filterData: JobFilterData
  filters: JobFilters
  onFilterChange: (key: keyof JobFilters, value: string) => void
  onSearchChange: (value: string) => void
  onClearFilters: () => void
  activeFilterCount: number
}

type AccordionKey = 'department' | 'location'

const JobsMobileFilters: React.FC<JobsMobileFiltersProps> = ({
  filterData,
  filters,
  onFilterChange,
  onSearchChange,
  onClearFilters,
  activeFilterCount,
}) => {
  const [isOpen, setIsOpen] = useState(false)
  const [accordion, setAccordion] = useState<Record<AccordionKey, boolean>>({
    department: false,
    location: false,
  })

  const toggleAccordion = (key: AccordionKey) => {
    setAccordion((prev) => ({ ...prev, [key]: !prev[key] }))
  }

  return (
    <div className="bg-gray-medium border border-white/15 lg:hidden">
      {/* Main toggle row */}
      <button onClick={() => setIsOpen(!isOpen)} className="flex w-full items-center justify-between px-24 py-20">
        <span className="fp-captital-title text-white">Filters</span>
        <div className="flex items-center gap-16">
          {activeFilterCount > 0 && <span className="fp-captital-title text-white/50">({activeFilterCount})</span>}
          <svg className={`h-24 w-24 text-white transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}>
            <use href="#icon-arrow-down" />
          </svg>
        </div>
      </button>

      {/* Collapsible body */}
      <div className={`fp-collapse ${isOpen ? 'open' : ''}`}>
        <div className="overflow-hidden">
          <div className="">
            {filterData.departments.length > 0 && (
              <JobMobileFilterAccordion
                label="Department"
                options={filterData.departments}
                selected={filters.department}
                isOpen={accordion.department}
                onToggle={() => toggleAccordion('department')}
                onChange={(val) => onFilterChange('department', val)}
              />
            )}

            {filterData.locations.length > 0 && (
              <JobMobileFilterAccordion
                label="Location"
                options={filterData.locations}
                selected={filters.location}
                isOpen={accordion.location}
                onToggle={() => toggleAccordion('location')}
                onChange={(val) => onFilterChange('location', val)}
              />
            )}

            <div className="px-24 py-20">
              <MobileSearchButton value={filters.search} onChange={onSearchChange} />
            </div>

            {/* Clear button */}
            {activeFilterCount > 0 && (
              <div className="px-24 py-16">
                <button onClick={onClearFilters} className="fp-btn-light w-full">
                  Clear all
                  <svg className="size-24">
                    <use href="#icon-close"></use>
                  </svg>
                </button>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  )
}

export default JobsMobileFilters
