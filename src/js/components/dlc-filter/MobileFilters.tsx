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

const MobileFilters: React.FC<MobileFiltersProps> = ({
  filterData,
  filters,
  onFilterChange,
  onClearFilters,
  activeFilterCount
}) => {
  const [isOpen, setIsOpen] = useState(false)
  const [accordion, setAccordion] = useState<AccordionState>({
    category: false,
    include: false,
    waterway: false
  })

  const toggleAccordion = (key: keyof AccordionState) => {
    setAccordion(prev => ({
      ...prev,
      [key]: !prev[key]
    }))
  }

  return (
    <div className="lg:hidden">
      {/* Mobile Filter Toggle */}
      <button
        onClick={() => setIsOpen(!isOpen)}
        className="w-full bg-gray-gunmetal border border-white/15 flex items-center justify-between px-24 py-20"
      >
        <span className="font-heading text-26 text-white">FILTERS</span>
        <div className="flex items-center gap-16">
          {activeFilterCount > 0 && (
            <span className="bg-accent text-black font-heading text-16 w-24 h-24 flex items-center justify-center rounded-full">
              {activeFilterCount}
            </span>
          )}
          <svg className={`w-24 h-24 text-white transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}>
            <use href="#icon-arrow-down" />
          </svg>
        </div>
      </button>

      {/* Mobile Filters Panel */}
      {isOpen && (
        <div className="bg-gray-gunmetal border border-white/15 p-0">
          {/* Category Filter - Collapsible */}
          <div className="border-b border-white/15">
            <button
              onClick={() => toggleAccordion('category')}
              className="w-full flex items-center justify-between px-24 py-20 bg-dark-gray hover:bg-white/5 transition-colors"
            >
              <span className="font-heading text-24 text-white uppercase">
                Category
              </span>
              <svg className={`w-24 h-24 text-white transition-transform duration-200 ${accordion.category ? 'rotate-180' : ''}`}>
                <use href="#icon-arrow-down" />
              </svg>
            </button>
            {accordion.category && (
              <div className="py-0">
                <button
                  onClick={() => onFilterChange('category', '')}
                  className="w-full text-left font-heading text-24 text-white uppercase px-24 py-16 flex items-center justify-between hover:bg-white/5 transition-colors border-b border-white/15"
                >
                  <span>All</span>
                  {filters.category === '' && (
                    <svg className="h-24 w-24 text-white">
                      <use href="#icon-check" />
                    </svg>
                  )}
                </button>
                {filterData.categories.map((cat, index) => (
                  <button
                    key={cat.slug}
                    onClick={() => onFilterChange('category', cat.slug)}
                    className={`w-full text-left font-heading text-24 uppercase px-24 py-16 hover:bg-white/5 transition-colors border-b border-white/15 last:border-b-0 ${filters.category === cat.slug ? 'text-sand' : 'text-white/70'}`}
                  >
                    {cat.name}
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* Pack Content Filter - Collapsible */}
          <div className="border-b border-white/15">
            <button
              onClick={() => toggleAccordion('include')}
              className="w-full flex items-center justify-between px-24 py-20 bg-dark-gray hover:bg-white/5 transition-colors"
            >
              <span className="font-heading text-24 text-white uppercase">
                Pack Content
              </span>
              <svg className={`w-24 h-24 text-white transition-transform duration-200 ${accordion.include ? 'rotate-180' : ''}`}>
                <use href="#icon-arrow-down" />
              </svg>
            </button>
            {accordion.include && (
              <div className="py-0">
                <button
                  onClick={() => onFilterChange('include', [])}
                  className="w-full text-left font-heading text-24 text-white uppercase px-24 py-16 flex items-center justify-between hover:bg-white/5 transition-colors border-b border-white/15"
                >
                  <span>All</span>
                  {filters.include.length === 0 && (
                    <svg className="h-24 w-24 text-white">
                      <use href="#icon-check" />
                    </svg>
                  )}
                </button>
                {filterData.includes.map((item, index) => {
                  const isSelected = filters.include.includes(item.slug)
                  return (
                    <button
                      key={item.slug}
                      onClick={() => {
                        const newSelection = isSelected
                          ? filters.include.filter(s => s !== item.slug)
                          : [...filters.include, item.slug]
                        onFilterChange('include', newSelection)
                      }}
                      className={`w-full text-left font-heading text-24 uppercase px-24 py-16 hover:bg-white/5 transition-colors border-b border-white/15 last:border-b-0 flex items-center justify-between ${isSelected ? 'text-sand' : 'text-white/70'}`}
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
          <div className="border-b border-white/15">
            <button
              onClick={() => toggleAccordion('waterway')}
              className="w-full flex items-center justify-between px-24 py-20 bg-dark-gray hover:bg-white/5 transition-colors"
            >
              <span className="font-heading text-24 text-white uppercase">
                Waterway
              </span>
              <svg className={`w-24 h-24 text-white transition-transform duration-200 ${accordion.waterway ? 'rotate-180' : ''}`}>
                <use href="#icon-arrow-down" />
              </svg>
            </button>
            {accordion.waterway && (
              <div className="py-0">
                <button
                  onClick={() => onFilterChange('waterway', [])}
                  className="w-full text-left font-heading text-24 text-white uppercase px-24 py-16 flex items-center justify-between hover:bg-white/5 transition-colors border-b border-white/15"
                >
                  <span>All</span>
                  {filters.waterway.length === 0 && (
                    <svg className="h-24 w-24 text-white">
                      <use href="#icon-check" />
                    </svg>
                  )}
                </button>
                {filterData.waterways.map((waterway, index) => {
                  const isSelected = filters.waterway.includes(waterway.slug)
                  return (
                    <button
                      key={waterway.slug}
                      onClick={() => {
                        const newSelection = isSelected
                          ? filters.waterway.filter(s => s !== waterway.slug)
                          : [...filters.waterway, waterway.slug]
                        onFilterChange('waterway', newSelection)
                      }}
                      className={`w-full text-left font-heading text-24 uppercase px-24 py-16 hover:bg-white/5 transition-colors border-b border-white/15 last:border-b-0 flex items-center justify-between ${isSelected ? 'text-sand' : 'text-white/70'}`}
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
          <div className="p-24">
            <button className="w-full fp-btn-corners h-64 bg-transparent flex items-center justify-between px-20" aria-label="Search">
              <span className="font-heading text-24 text-white uppercase">Search</span>
              <svg className="h-24 w-24 text-white">
                <use href="#icon-search" />
              </svg>
            </button>
          </div>
        </div>
      )}
    </div>
  )
}

export default MobileFilters
