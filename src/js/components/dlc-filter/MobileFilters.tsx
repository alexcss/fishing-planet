import React, { useState } from 'react'
import type { FilterData, FilterOption, Filters } from './types'
import FilterAccordion from './FilterAccordion'

interface MobileFiltersProps {
  filterData: FilterData
  filters: Filters
  onFilterChange: (key: keyof Filters, value: string | string[]) => void
  onClearFilters: () => void
  activeFilterCount: number
}

type AccordionKey = 'category' | 'include' | 'waterway'

const MobileFilters: React.FC<MobileFiltersProps> = ({ filterData, filters, onFilterChange, onClearFilters, activeFilterCount }) => {
  const [isOpen, setIsOpen] = useState(true)
  const [accordion, setAccordion] = useState<Record<AccordionKey, boolean>>({
    category: false,
    include: false,
    waterway: false,
  })

  const toggleAccordion = (key: AccordionKey) => {
    setAccordion((prev) => ({ ...prev, [key]: !prev[key] }))
  }

  const filterSections: Array<{ key: AccordionKey; label: string; options: FilterOption[]; multi?: boolean }> = [
    { key: 'category', label: 'Category', options: filterData.categories },
    { key: 'include', label: 'Pack Content', options: filterData.includes, multi: true },
    { key: 'waterway', label: 'Waterway', options: filterData.waterways, multi: true },
  ]

  return (
    <div className="border border-white/15 bg-black lg:hidden">
      {/* Mobile Filter Toggle */}
      <button
        onClick={() => setIsOpen(!isOpen)}
        className={`flex w-full items-center justify-between px-24 py-22 ${!isOpen ? 'text-white' : 'text-white/50'}`}
      >
        <span className="fp-captital-title">Filters</span>
        <div className="flex items-center gap-16">
          {activeFilterCount > 0 && <span className="font-heading text-24/none">({activeFilterCount})</span>}
          <svg className={`h-24 w-24 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}>
            <use href="#icon-arrow-down" />
          </svg>
        </div>
      </button>

      {/* Mobile Filters Panel */}
      <div className={`fp-collapse ${isOpen ? 'open' : ''}`}>
        <div className="overflow-hidden">
          <div className="space-y-8 px-24">
            {filterSections.map(({ key, label, options, multi }) => (
              <FilterAccordion
                key={key}
                label={label}
                isOpen={accordion[key]}
                onToggle={() => toggleAccordion(key)}
                options={options}
                selected={filters[key]}
                multi={multi}
                onChange={(val) => onFilterChange(key, val)}
              />
            ))}

            {/* Search Button */}
            <div className="py-24">
              <button className="fp-btn-corners mt-8 flex h-64 w-full items-center justify-between bg-transparent px-20" aria-label="Search">
                <span className="fp-captital-title">Search</span>
                <svg className="h-24 w-24 text-white">
                  <use href="#icon-search" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default MobileFilters
