import React, { useState } from 'react'
import type { FilterData, FilterOption, Filters } from './types'
import FilterAccordion from './FilterAccordion'
import MobileFilterToggle from './MobileFilterToggle'
import MobileSearchButton from './MobileSearchButton'
import MobileClearButton from './MobileClearButton'

interface MobileFiltersProps {
  filterData: FilterData
  filters: Filters
  onFilterChange: (key: keyof Filters, value: string | string[]) => void
  onClearFilters: () => void
  activeFilterCount: number
}

type AccordionKey = 'category' | 'include' | 'waterway'

const filterSections = (filterData: FilterData): Array<{ key: AccordionKey; label: string; options: FilterOption[]; multi?: boolean }> => [
  { key: 'category', label: 'Category', options: filterData.categories },
  { key: 'include', label: 'Pack Content', options: filterData.includes, multi: true },
  { key: 'waterway', label: 'Waterway', options: filterData.waterways, multi: true },
]

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

  return (
    <div className="border border-white/15 bg-black lg:hidden">
      <MobileFilterToggle
        isOpen={isOpen}
        activeFilterCount={activeFilterCount}
        onToggle={() => setIsOpen(!isOpen)}
      />

      <div className={`fp-collapse ${isOpen ? 'open' : ''}`}>
        <div className="overflow-hidden">
          <div className="space-y-8 px-24">
            {filterSections(filterData).map(({ key, label, options, multi }) => (
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

            <div className="flex flex-col gap-8 py-24">
              {activeFilterCount > 0 && <MobileClearButton onClearFilters={onClearFilters} />}
              <MobileSearchButton />
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default MobileFilters
