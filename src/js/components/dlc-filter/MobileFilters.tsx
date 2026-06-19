import React, { useState } from 'react'
import type { FilterData, Filters } from './types'

interface MobileFiltersProps {
  filterData: FilterData
  filters: Filters
  onFilterChange: (key: keyof Filters, value: string | string[]) => void
  onClearFilters: () => void
  activeFilterCount: number
}

type AccordionState = {
  category: boolean
  include: boolean
  waterway: boolean
}

const MobileFilters: React.FC<MobileFiltersProps> = ({ filterData, filters, onFilterChange, onClearFilters, activeFilterCount }) => {
  const [isOpen, setIsOpen] = useState(true)
  const [accordion, setAccordion] = useState<AccordionState>({
    category: false,
    include: false,
    waterway: false,
  })

  const toggleAccordion = (key: keyof AccordionState) => {
    setAccordion((prev) => ({
      ...prev,
      [key]: !prev[key],
    }))
  }

  return (
    <div className="border border-white/15 bg-black lg:hidden">
      {/* Mobile Filter Toggle */}
      <button
        onClick={() => setIsOpen(!isOpen)}
        className={`flex w-full items-center justify-between px-24 py-20 ${!isOpen ? 'text-white' : 'text-white/50'}`}
      >
        <span className="fp-captital-title">Filters</span>
        <div className="flex items-center gap-16">
          {activeFilterCount > 0 && (
            <span className="bg-accent font-heading text-16 flex h-24 w-24 items-center justify-center rounded-full text-black">
              {activeFilterCount}
            </span>
          )}
          <svg className={`h-24 w-24 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}>
            <use href="#icon-arrow-down" />
          </svg>
        </div>
      </button>

      {/* Mobile Filters Panel */}
      <div className={`fp-collapse ${isOpen ? 'open' : ''}`}>
        <div className="overflow-hidden">
          <div className="space-y-8 px-24">
            {/* Category Filter - Collapsible */}
            <div className="">
              <button
                onClick={() => toggleAccordion('category')}
                className="bg-gray-gunmetal flex w-full items-center justify-between border border-white/15 p-12"
              >
                <span className="fp-captital-title">Category</span>
                <svg className={`h-24 w-24 text-white transition-transform duration-200 ${accordion.category ? 'rotate-180' : ''}`}>
                  <use href="#icon-arrow-down" />
                </svg>
              </button>
              {accordion.category && (
                <div>
                  <button
                    onClick={() => onFilterChange('category', '')}
                    className="font-heading text-24 flex w-full items-center justify-between border-b border-white/15 px-24 py-16 text-left text-white uppercase transition-colors hover:bg-white/5"
                  >
                    <span>All</span>
                    {filters.category === '' && (
                      <svg className="h-24 w-24 text-white">
                        <use href="#icon-check" />
                      </svg>
                    )}
                  </button>
                  {filterData.categories.map((cat) => (
                    <button
                      key={cat.slug}
                      onClick={() => onFilterChange('category', cat.slug)}
                      className={`font-heading text-24 w-full border-b border-white/15 px-24 py-16 text-left uppercase transition-colors last:border-b-0 hover:bg-white/5 ${filters.category === cat.slug ? 'text-sand' : 'text-white/70'}`}
                    >
                      {cat.name}
                    </button>
                  ))}
                </div>
              )}
            </div>

            {/* Pack Content Filter - Collapsible */}
            <div className="">
              <button
                onClick={() => toggleAccordion('include')}
                className="bg-gray-gunmetal flex w-full items-center justify-between border border-white/15 p-12"
              >
                <span className="fp-captital-title">Pack Content</span>
                <svg className={`h-24 w-24 text-white transition-transform duration-200 ${accordion.include ? 'rotate-180' : ''}`}>
                  <use href="#icon-arrow-down" />
                </svg>
              </button>
              {accordion.include && (
                <div>
                  <button
                    onClick={() => onFilterChange('include', [])}
                    className="font-heading text-24 flex w-full items-center justify-between border-b border-white/15 px-24 py-16 text-left text-white uppercase transition-colors hover:bg-white/5"
                  >
                    <span>All</span>
                    {filters.include.length === 0 && (
                      <svg className="h-24 w-24 text-white">
                        <use href="#icon-check" />
                      </svg>
                    )}
                  </button>
                  {filterData.includes.map((item) => {
                    const isSelected = filters.include.includes(item.slug)
                    return (
                      <button
                        key={item.slug}
                        onClick={() => {
                          const newSelection = isSelected ? filters.include.filter((s) => s !== item.slug) : [...filters.include, item.slug]
                          onFilterChange('include', newSelection)
                        }}
                        className={`font-heading text-24 flex w-full items-center justify-between border-b border-white/15 px-24 py-16 text-left uppercase transition-colors last:border-b-0 hover:bg-white/5 ${isSelected ? 'text-sand' : 'text-white/70'}`}
                      >
                        <span>{item.name}</span>
                        {isSelected && (
                          <svg className="h-24 w-24 text-white">
                            <use href="#icon-check" />
                          </svg>
                        )}
                      </button>
                    )
                  })}
                </div>
              )}
            </div>

            {/* Waterway Filter - Collapsible */}
            <div className="">
              <button
                onClick={() => toggleAccordion('waterway')}
                className="bg-gray-gunmetal flex w-full items-center justify-between border border-white/15 p-12"
              >
                <span className="fp-captital-title">Waterway</span>
                <svg className={`h-24 w-24 text-white transition-transform duration-200 ${accordion.waterway ? 'rotate-180' : ''}`}>
                  <use href="#icon-arrow-down" />
                </svg>
              </button>
              {accordion.waterway && (
                <div>
                  <button
                    onClick={() => onFilterChange('waterway', [])}
                    className="font-heading text-24 flex w-full items-center justify-between border-b border-white/15 px-24 py-16 text-left text-white uppercase transition-colors hover:bg-white/5"
                  >
                    <span>All</span>
                    {filters.waterway.length === 0 && (
                      <svg className="h-24 w-24 text-white">
                        <use href="#icon-check" />
                      </svg>
                    )}
                  </button>
                  {filterData.waterways.map((waterway) => {
                    const isSelected = filters.waterway.includes(waterway.slug)
                    return (
                      <button
                        key={waterway.slug}
                        onClick={() => {
                          const newSelection = isSelected ? filters.waterway.filter((s) => s !== waterway.slug) : [...filters.waterway, waterway.slug]
                          onFilterChange('waterway', newSelection)
                        }}
                        className={`font-heading text-24 flex w-full items-center justify-between border-b border-white/15 px-24 py-16 text-left uppercase transition-colors last:border-b-0 hover:bg-white/5 ${isSelected ? 'text-sand' : 'text-white/70'}`}
                      >
                        <span>{waterway.name}</span>
                        {isSelected && (
                          <svg className="h-24 w-24 text-white">
                            <use href="#icon-check" />
                          </svg>
                        )}
                      </button>
                    )
                  })}
                </div>
              )}
            </div>

            {/* Search Button */}
            <div className="pb-24">
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
