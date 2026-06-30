import React, { useState, useRef, useEffect } from 'react'
import type { JobFilterOption } from './types'

interface JobFilterDropdownProps {
  label: string
  options: JobFilterOption[]
  selected: string
  onSelect: (value: string) => void
}

const JobFilterDropdown: React.FC<JobFilterDropdownProps> = ({ label, options, selected, onSelect }) => {
  const [isOpen, setIsOpen] = useState(false)
  const dropdownRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    const handleClickOutside = (e: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(e.target as Node)) {
        setIsOpen(false)
      }
    }
    document.addEventListener('mousedown', handleClickOutside)
    return () => document.removeEventListener('mousedown', handleClickOutside)
  }, [])

  const selectedOption = options.find((o) => o.slug === selected)
  const displayValue = selectedOption ? selectedOption.name : 'All'

  const handleSelect = (slug: string) => {
    onSelect(slug)
    setIsOpen(false)
  }

  return (
    <div ref={dropdownRef} className="relative">
      {/* Trigger */}
      <button
        onClick={() => setIsOpen(!isOpen)}
        className="flex h-64 w-full items-center justify-between gap-12 border border-white/15 bg-transparent px-24 py-20 text-left transition-colors hover:border-white/30"
      >
        <span className="fp-captital-title-sm min-w-0 truncate uppercase">
          <span className="text-white/50">{label}:</span> <span className="text-white">{displayValue}</span>
        </span>
        <svg className={`h-24 w-24 shrink-0 text-white transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}>
          <use href="#icon-arrow-down" />
        </svg>
      </button>

      {/* Dropdown Panel */}
      {isOpen && (
        <div className="bg-gray-medium absolute top-full right-0 left-0 z-50 mt-8 border border-white/15 py-8 shadow-2xl">
          {/* All option */}
          <button
            onClick={() => handleSelect('')}
            className="flex w-full items-center justify-between px-24 py-12 transition-colors hover:bg-white/5"
          >
            <span className={`fp-captital-title-sm uppercase ${!selected ? 'text-white' : 'text-white/50'}`}>All</span>
            {!selected && (
              <svg className="h-24 w-24 text-white">
                <use href="#icon-check" />
              </svg>
            )}
          </button>

          {/* Options */}
          <div className="fp-scrollbar-thin max-h-320 overflow-y-auto">
            {options.map((option) => (
              <button key={option.slug} onClick={() => handleSelect(option.slug)} className="block w-full px-24 hover:bg-white/5">
                <span className="flex items-center justify-between border-t border-white/15 py-12">
                  <span className={`fp-captital-title-sm ${selected === option.slug ? 'text-white' : 'text-white/50'}`}>{option.name}</span>
                  {selected === option.slug && (
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

export default JobFilterDropdown
