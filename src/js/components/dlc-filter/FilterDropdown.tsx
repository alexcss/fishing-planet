import React, { useState, useRef, useEffect } from 'react'
import type { FilterOption } from './types'

interface FilterDropdownProps {
  label: string
  options: FilterOption[]
  selected: string | string[]
  onSelect: (value: string | string[]) => void
  searchPlaceholder?: string
  showSearch?: boolean
  multi?: boolean
}

const FilterDropdown: React.FC<FilterDropdownProps> = ({
  label,
  options,
  selected,
  onSelect,
  searchPlaceholder = 'Search...',
  showSearch = false,
  multi = false,
}) => {
  const [isOpen, setIsOpen] = useState(false)
  const [searchQuery, setSearchQuery] = useState('')
  const dropdownRef = useRef<HTMLDivElement>(null)

  // Close dropdown when clicking outside
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setIsOpen(false)
      }
    }

    document.addEventListener('mousedown', handleClickOutside)
    return () => document.removeEventListener('mousedown', handleClickOutside)
  }, [])

  const filteredOptions = searchQuery ? options.filter((opt) => opt.name.toLowerCase().includes(searchQuery.toLowerCase())) : options

  // Normalize selected to array for internal use
  const selectedArray = multi ? (selected as string[]) : selected ? [selected as string] : []

  const selectedOption = multi ? null : options.find((opt) => opt.slug === selected)

  // Display text for button
  const displayLabel = multi
    ? selectedArray.length > 0
      ? `${label} (${selectedArray.length})`
      : label
    : selectedOption
      ? selectedOption.name
      : label

  // Toggle selection for multi-select
  const toggleSelection = (slug: string) => {
    if (!multi) {
      onSelect(slug)
      return
    }

    const current = selected as string[]
    const isSelected = current.includes(slug)
    let newSelection: string[]

    if (isSelected) {
      newSelection = current.filter((s) => s !== slug)
    } else {
      newSelection = [...current, slug]
    }

    onSelect(newSelection)
  }

  // Check if option is selected
  const isSelected = (slug: string): boolean => {
    if (multi) {
      return (selected as string[]).includes(slug)
    }
    return selected === slug
  }

  return (
    <div ref={dropdownRef} className="relative w-220 xl:w-280">
      {/* Dropdown Button */}
      <button
        onClick={() => setIsOpen(!isOpen)}
        className="flex h-64 w-full items-center justify-between gap-12 border border-white/15 bg-black px-24 py-20 text-left transition-colors hover:border-white/30"
      >
        <span className="fp-captital-title min-w-0 truncate" dangerouslySetInnerHTML={{ __html: displayLabel }}></span>
        <svg className={`h-24 w-24 shrink-0 text-white transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}>
          <use href="#icon-arrow-down" />
        </svg>
      </button>

      {/* Dropdown Menu */}
      {isOpen && (
        <div className="absolute top-full right-0 left-0 z-50 mt-4 border border-white/15 bg-black shadow-2xl">
          {/* Search Input */}
          {showSearch && (
            <div className="border-b border-white/15 px-20 py-16">
              <div className="flex items-center gap-12">
                <svg className="h-24 w-24 text-white/50">
                  <use href="#icon-search" />
                </svg>
                <span className="font-heading text-24 text-white/50 uppercase">{searchPlaceholder}</span>
              </div>
            </div>
          )}

          {/* Options List */}
          <div className="fp-scrollbar-thin max-h-320 overflow-y-auto pb-20">
            {/* All Option - clears selection for multi, selects empty for single */}
            <button
              onClick={() => {
                if (multi) {
                  onSelect([])
                } else {
                  onSelect('')
                }
              }}
              className="w-full px-20 transition-colors hover:bg-white/5"
            >
              <span className="flex items-center justify-between border-b border-white/15 py-16">
                <span className={`font-heading text-24 leading-none uppercase ${selectedArray.length === 0 ? 'text-white' : 'text-white/50'}`}>
                  All
                </span>
                {selectedArray.length === 0 && (
                  <svg className="h-24 w-24 text-white">
                    <use href="#icon-check" />
                  </svg>
                )}
              </span>
            </button>

            {/* Filtered Options */}
            {filteredOptions.map((option) => (
              <button key={option.slug} onClick={() => toggleSelection(option.slug)} className="w-full px-20 transition-colors hover:bg-white/5">
                <span className="flex items-center justify-between border-b border-white/15 py-16">
                  <span
                    className={`font-heading text-24 text-left leading-none uppercase ${isSelected(option.slug) ? 'text-white' : 'text-white/50'}`}
                    dangerouslySetInnerHTML={{ __html: option.name }}
                  ></span>
                  {isSelected(option.slug) && (
                    <svg className="h-24 w-24 text-white">
                      <use href="#icon-check" />
                    </svg>
                  )}
                </span>
              </button>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}

export default FilterDropdown
