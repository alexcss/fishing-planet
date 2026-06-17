import React, { useState } from 'react'
import type { FilterData, Filters } from './types'

interface MobileFiltersProps {
  filterData: FilterData
  filters: Filters
  onFilterChange: (key: keyof Filters, value: string) => void
  onClearFilters: () => void
  activeFilterCount: number
}

const MobileFilters: React.FC<MobileFiltersProps> = ({
  filterData,
  filters,
  onFilterChange,
  onClearFilters,
  activeFilterCount
}) => {
  const [isOpen, setIsOpen] = useState(false)

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
          <svg
            className={`w-24 h-24 text-white transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}
            viewBox="0 0 24 24"
            fill="none"
          >
            <path d="M12 15L6 9H18L12 15Z" fill="currentColor" />
          </svg>
        </div>
      </button>

      {/* Mobile Filters Panel */}
      {isOpen && (
        <div className="bg-gray-gunmetal border border-white/15 p-24 space-y-24">
      {/* Category Filter - Mobile */}
      <div>
        <label className="block font-heading text-24 text-white mb-16">Category</label>
        <div className="space-y-12">
          <button
            onClick={() => onFilterChange('category', '')}
            className={`w-full text-left font-heading text-24 ${filters.category === '' ? 'text-white' : 'text-white/70'}`}
          >
            All
          </button>
          {filterData.categories.map(cat => (
            <button
              key={cat.slug}
              onClick={() => onFilterChange('category', cat.slug)}
              className={`w-full text-left font-heading text-24 ${filters.category === cat.slug ? 'text-sand' : 'text-sand'}`}
            >
              {cat.name} ({cat.count})
            </button>
          ))}
        </div>
      </div>

      {/* Pack Content Filter - Mobile */}
      <div>
        <label className="block font-heading text-24 text-white mb-16">Pack Content</label>
        <div className="space-y-12">
          <button
            onClick={() => onFilterChange('include', '')}
            className={`w-full text-left font-heading text-24 ${filters.include === '' ? 'text-white' : 'text-white/70'}`}
          >
            All
          </button>
          {filterData.includes.map(item => (
            <button
              key={item.slug}
              onClick={() => onFilterChange('include', item.slug)}
              className={`w-full text-left font-heading text-24 ${filters.include === item.slug ? 'text-sand' : 'text-sand'}`}
            >
              {item.name} ({item.count})
            </button>
          ))}
        </div>
      </div>

      {/* Waterway Filter - Mobile */}
      <div>
        <label className="block font-heading text-24 text-white mb-16">Waterway</label>
        <div className="space-y-12">
          <button
            onClick={() => onFilterChange('waterway', '')}
            className={`w-full text-left font-heading text-24 ${filters.waterway === '' ? 'text-white' : 'text-white/70'}`}
          >
            All
          </button>
          {filterData.waterways.map(waterway => (
            <button
              key={waterway.slug}
              onClick={() => onFilterChange('waterway', waterway.slug)}
              className={`w-full text-left font-heading text-24 flex justify-between ${filters.waterway === waterway.slug ? 'text-sand' : 'text-sand'}`}
            >
              <span>{waterway.name}</span>
              <span className="text-white/30">({waterway.count})</span>
            </button>
          ))}
        </div>
      </div>

      {/* Clear Filters */}
      {activeFilterCount > 0 && (
        <button
          onClick={onClearFilters}
          className="w-full fp-btn py-16"
        >
          Clear Filters
        </button>
      )}
        </div>
      )}
    </div>
  )
}

export default MobileFilters
