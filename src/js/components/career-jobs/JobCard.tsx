import React from 'react'
import type { JobPost } from './types'

interface JobCardProps {
  job: JobPost
}

const JobCard: React.FC<JobCardProps> = ({ job }) => {

  return (
    <a
      href={job.permalink}
      className={`group bg-gray-gunmetal hover:bg-accent relative flex h-full min-h-280 flex-col justify-between p-32 xl:min-h-324`}
    >
      <h3 className="h4 flex-1">{job.title}</h3>

      <div className="mt-50 flex items-end justify-between gap-24">
        <div className="flex flex-col gap-12">
          {job.departments.length > 0 && (
            <div className="flex items-center gap-12">
              <span className="fp-btn-corners bg-black-true/5 flex-none transition-colors group-hover:bg-white/10">
                <span>
                  <svg className="h-18 w-18">
                    <use href="#icon-case" />
                  </svg>
                </span>
              </span>
              <span className="fp-heading-text text-24/none">{job.departments.map((d) => d.name).join(', ')}</span>
            </div>
          )}

          {job.locations.length > 0 && (
            <div className="flex items-center gap-12">
              <span className="fp-btn-corners bg-black-true/5 flex-none transition-colors group-hover:bg-white/10">
                <span>
                  <svg className="h-18 w-18">
                    <use href="#icon-location" />
                  </svg>
                </span>
              </span>
              <span className="fp-heading-text text-24/none">{job.locations.map((l) => l.name).join(', ')}</span>
            </div>
          )}
        </div>

        <span
          className={`text-accent flex size-50 shrink-0 items-center justify-center bg-white opacity-0 transition-opacity group-hover:opacity-100`}
          aria-hidden="true"
        >
          <svg className="size-24">
            <use href="#icon-arrow-r" />
          </svg>
        </span>
      </div>
    </a>
  )
}

export default JobCard
